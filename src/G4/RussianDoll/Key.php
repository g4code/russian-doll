<?php

namespace G4\RussianDoll;

use \G4\Constants\CacheLifetime;

class Key
{

    private $belongsTo;

    /**
     * @var int
     */
    private $cacheLifetime;

    private $fixedPartSufix;

    private $variableParts;


    public function __construct($fixedPartSufix = '')
    {
        $this->setFixedPartSuffix($fixedPartSufix);
        $this->belongsTo     = [];
        $this->cacheLifetime = CacheLifetime::TILL_THE_END_OF_TIME;
        $this->variableParts = [];
    }

    public function addBelongsTo(\G4\RussianDoll\Key $key)
    {
        $this->belongsTo[] = $key;
        return $this;
    }

    /**
     * @param string $value
     * @return \G4\RussianDoll\Key
     */
    public function addVariablePart($value)
    {
        $this->variableParts[] = $value;
        return $this;
    }

    public function appendToFixedPartSufix($value)
    {
        $this->fixedPartSufix = join(
            \G4\RussianDoll\Digestor::DELIMITER,
            array($this->fixedPartSufix, $value)
        );
        return $this;
    }

    public function getBelongsTo()
    {
        return $this->belongsTo;
    }

    public function getCacheLifetime()
    {
        return $this->cacheLifetime;
    }

    public function getFixedPart()
    {
        return join(
            \G4\RussianDoll\Digestor::DELIMITER,
            array(get_class($this), $this->fixedPartSufix)
        );
    }

    public function getFixedPartSufix()
    {
        return $this->fixedPartSufix;
    }

    public function getVariableParts()
    {
        return $this->variableParts;
    }

    public function setBelongsTo(array $belongsTo)
    {
        $this->belongsTo = $belongsTo;
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
        $this->variableParts = $value;
        return $this;
    }

    private function setFixedPartSuffix($value)
    {
        $this->fixedPartSufix = is_array($value)
            ? join(\G4\RussianDoll\Digestor::DELIMITER, $value)
            : $value;
        return $this;
    }
}