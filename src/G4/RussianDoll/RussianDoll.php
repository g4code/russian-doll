<?php

namespace G4\RussianDoll;

use \G4\Mcache\Mcache;

class RussianDoll
{

    /**
     * @var string
     */
    private $_digestedKey;

    /**
     * @var \G4\RussianDoll\Digestor
     */
    private $_digestor;

    /**
     * @var \G4\RussianDoll\Key
     */
    private $_key;

    /**
     * @var \G4\Mcache\Mcache
     */
    private $_mcache;


    public function __construct(\G4\Mcache\Mcache $mcache)
    {
        $this->_mcache = $mcache;
    }

    public function fetch()
    {
        return $this->_mcache
            ->id($this->_getDigestedKey())
            ->get();
    }

    public function setKey(\G4\RussianDoll\Key $key)
    {
        $this->_key = $key;
        return $this;
    }

    public function write($data)
    {
        $this->_mcache
            ->object($data)
            ->id($this->_getDigestedKey())
            ->expiration($this->_getKeyInstance()->getCacheLifetime())
            ->set();
    }

    public function expire()
    {
        unset($this->_digestedKey);
        $this->_getDigestorInstance()->setNewTimePart();
    }

    /**
     * @return \G4\RussianDoll\Digestor
     */
    private function _getDigestorInstance()
    {
        if (!$this->_digestor instanceof \G4\RussianDoll\Digestor) {
            $this->_digestor = new \G4\RussianDoll\Digestor($this->_getKeyInstance(), $this->_mcache);
        }
        return $this->_digestor;
    }

    /**
     * @return string
     */
    private function _getDigestedKey()
    {
        if (!isset($this->_digestedKey)) {
            $this->_digestedKey = $this->_getDigestorInstance()->digest();
        }
        return $this->_digestedKey;
    }

    /**
     * @throws \Exception
     * @return \G4\RussianDoll\Key
     */
    private function _getKeyInstance()
    {
        if (!$this->_key instanceof \G4\RussianDoll\Key) {
            throw new \Exception('Key is not set and must be an instance of \G4\RussianDoll\Key');
        }
        return $this->_key;
    }
}