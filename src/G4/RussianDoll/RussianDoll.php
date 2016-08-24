<?php

namespace G4\RussianDoll;

use \G4\Mcache\Mcache;

class RussianDoll
{

    /**
     * @var string
     */
    private $digestedKey;

    /**
     * @var \G4\RussianDoll\Digestor
     */
    private $digestor;

    /**
     * @var \G4\RussianDoll\Key
     */
    private $key;

    /**
     * @var \G4\Mcache\Mcache
     */
    private $mcache;


    public function __construct(\G4\Mcache\Mcache $mcache)
    {
        $this->mcache = $mcache;
    }

    public function fetch()
    {
        return $this->mcache
            ->id($this->getDigestedKey())
            ->get();
    }

    public function setKey(\G4\RussianDoll\Key $key)
    {
        $this->key = $key;
        unset($this->digestor);
        return $this;
    }

    public function write($data)
    {
        $this->mcache
            ->object($data)
            ->id($this->getDigestedKey())
            ->expiration($this->getKeyInstance()->getCacheLifetime())
            ->set();
    }

    public function expire()
    {
        unset($this->digestedKey);
        $this->getDigestorInstance()->expire();
    }

    /**
     * @return \G4\RussianDoll\Digestor
     */
    private function getDigestorInstance()
    {
        if (!$this->digestor instanceof \G4\RussianDoll\Digestor) {
            $this->digestor = new \G4\RussianDoll\Digestor($this->getKeyInstance(), $this->mcache);
        }
        return $this->digestor;
    }

    /**
     * @return string
     */
    private function getDigestedKey()
    {
        if (!isset($this->digestedKey)) {
            $this->digestedKey = $this->getDigestorInstance()->digest();
        }
        return $this->digestedKey;
    }

    /**
     * @throws \Exception
     * @return \G4\RussianDoll\Key
     */
    private function getKeyInstance()
    {
        if (!$this->key instanceof \G4\RussianDoll\Key) {
            throw new \Exception('Key is not set and must be an instance of \G4\RussianDoll\Key');
        }
        return $this->key;
    }
}