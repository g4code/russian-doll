<?php

namespace G4\RussianDoll;

use \G4\RussianDoll\Key;
use \G4\Mcache\Mcache;

class Digestor
{

    const DELIMITER = '|';

    /**
     * @var \G4\RussianDoll\Key
     */
    private $_key;

    /**
     * @var \G4\Mcache\Mcache
     */
    private $_mcache;

    /**
     * unix timestamp with microseconds
     * @var string
     */
    private $_timePart;

    /**
     * @var string
     */
    private $_timePartKey;


    public function __construct(\G4\RussianDoll\Key $key, \G4\Mcache\Mcache $mcache)
    {
        $this->_key         = $key;
        $this->_mcache      = $mcache;
        $this->_timePartKey = null;
    }

    public function digest()
    {
        return join(
            self::DELIMITER,
            array(
                $this->_getTimePartKey(),
                $this->_getVariableParts(),
                $this->_getTimePart()
            )
        );
    }

    public function setNewTimePart()
    {
        $this->_timePart = microtime();

        $this->_mcache
            ->id($this->_getTimePartKey())
            ->object($this->_timePart)
            ->set();

        $this->_setNewTimePartOnDependencies();
    }

    private function _getKeyDependencies()
    {
        $dependencies = $this->_key->getBelongsTo();

        return is_array($dependencies)
            ? $dependencies
            : array();
    }

    private function _getVariableParts()
    {
        return join(
            self::DELIMITER,
            $this->_key->getVariableParts()
        );
    }

    private function _getTimePart()
    {
        $this->_timePart = $this->_mcache->id($this->_getTimePartKey())->get();

        if (empty($this->_timePart)) {
            $this->setNewTimePart();
        }

        return $this->_timePart;
    }

    private function _getTimePartKey()
    {
        if ($this->_timePartKey === null) {
            $this->_timePartKey = join(
                self::DELIMITER,
                (array(__CLASS__, $this->_key->getFixedPart()))
            );
        }
        return $this->_timePartKey;
    }

    private function _setNewTimePartOnDependencies()
    {
        foreach($this->_getKeyDependencies() as $key) {
            $this->_setNewTimePartOnOneDependentKey($key);
        }
    }

    private function _setNewTimePartOnOneDependentKey(\G4\RussianDoll\Key $key)
    {
        $digestor = new self($key, $this->_mcache);
        $digestor
            ->setNewTimePart();
    }
}