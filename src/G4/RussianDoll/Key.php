<?php

namespace G4\RussianDoll;

class Key
{
    const DEFAULT_CACHE_LIFE_TIME = 86400;

    /**
     * @var array
     */
    private $belongsTo = [];

    /**
     * @var array
     */
    private $fixedPartSufix = [];

    /**
     * @var array
     */
    private $variableParts = [];

    /**
     * @var int
     */
    private $cacheLifetime = self::DEFAULT_CACHE_LIFE_TIME;

    public function __construct(...$fixedPartSufix)
    {
        $this->setFixedPartSuffix($fixedPartSufix);
    }

    public function addBelongsTo(Key $key)
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
        $this->fixedPartSufix = join(Digestor::DELIMITER, [$this->fixedPartSufix, $value]);
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
        return join(Digestor::DELIMITER, [get_class($this), $this->fixedPartSufix]);
    }

    public function getFixedPartSufix()
    {
        return $this->fixedPartSufix;
    }

    public function getVariableParts()
    {
        return $this->variableParts;
    }

    public function hasVariableParts()
    {
        return count($this->variableParts) > 0;
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
        $this->fixedPartSufix = join(Digestor::DELIMITER, $value);
        return $this;
    }
}