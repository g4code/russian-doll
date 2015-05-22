<?php

namespace G4\RussianDoll;

use \G4\Constants\CacheLifetime;

class Key
{

    private $_belongsTo;

    /**
     * @var int
     */
    private $cacheLifetime;

    private $_fixedPartSufix;

    private $_variableParts;


    public function __construct($fixedPartSufix = '')
    {
        $this->_setFixedPartSuffix($fixedPartSufix);
        $this->_belongsTo     = [];
        $this->cacheLifetime  = CacheLifetime::TILL_THE_END_OF_TIME;
        $this->_variableParts = [];
    }

    public function addBelongsTo(\G4\RussianDoll\Key $key)
    {
        $this->_belongsTo[] = $key;
        return $this;
    }

    /**
     * @param string $value
     * @return \G4\RussianDoll\Key
     */
    public function addVariablePart($value)
    {
        $this->_variableParts[] = $value;
        return $this;
    }

    public function appendToFixedPartSufix($value)
    {
        $this->_fixedPartSufix = join(
            \G4\RussianDoll\Digestor::DELIMITER,
            array($this->_fixedPartSufix, $value)
        );
        return $this;
    }

    public function getBelongsTo()
    {
        return $this->_belongsTo;
    }

    public function getCacheLifetime()
    {
        return $this->cacheLifetime;
    }

    public function getFixedPart()
    {
        return join(
            \G4\RussianDoll\Digestor::DELIMITER,
            array(get_class($this), $this->_fixedPartSufix)
        );
    }

    public function getFixedPartSufix()
    {
        return $this->_fixedPartSufix;
    }

    public function getVariableParts()
    {
        return $this->_variableParts;
    }

    public function setBelongsTo(array $belongsTo)
    {
        $this->_belongsTo = $belongsTo;
        return $this;
    }

    /**
     * @param int $cacheLifetime
     * @return \G4\RussianDoll\Key
     */
    public function setCacheLifetime($cacheLifetime)
    {
        $this->cacheLifetime = $cacheLifetime;
        return $this;
    }

    /**
     * @param array $value
     * @return \G4\RussianDoll\Key
     */
    public function setVariableParts(array $value)
    {
        $this->_variableParts = $value;
        return $this;
    }

    private function _setFixedPartSuffix($value)
    {
        $this->_fixedPartSufix = is_array($value)
            ? join(\G4\RussianDoll\Digestor::DELIMITER, $value)
            : $value;
        return $this;
    }
}