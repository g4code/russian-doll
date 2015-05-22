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
    private $key;

    /**
     * @var \G4\Mcache\Mcache
     */
    private $mcache;

    /**
     * unix timestamp with microseconds
     * @var string
     */
    private $timePart;

    /**
     * @var string
     */
    private $timePartKey;


    public function __construct(\G4\RussianDoll\Key $key, \G4\Mcache\Mcache $mcache)
    {
        $this->key         = $key;
        $this->mcache      = $mcache;
        $this->timePartKey = null;
    }

    public function digest()
    {
        return join(
            self::DELIMITER,
            array(
                $this->getTimePartKey(),
                $this->getVariableParts(),
                $this->getTimePart()
            )
        );
    }

    public function setNewTimePart()
    {
        $this->timePart = microtime();

        $this->mcache
            ->id($this->getTimePartKey())
            ->object($this->timePart)
            ->expiration($this->key->getCacheLifetime())
            ->set();

        $this->setNewTimePartOnDependencies();
    }

    private function getKeyDependencies()
    {
        $dependencies = $this->key->getBelongsTo();

        return is_array($dependencies)
            ? $dependencies
            : array();
    }

    private function getVariableParts()
    {
        return join(
            self::DELIMITER,
            $this->key->getVariableParts()
        );
    }

    private function getTimePart()
    {
        $this->timePart = $this->mcache->id($this->getTimePartKey())->get();

        if (empty($this->timePart)) {
            $this->setNewTimePart();
        }

        return $this->timePart;
    }

    private function getTimePartKey()
    {
        if ($this->timePartKey === null) {
            $this->timePartKey = join(
                self::DELIMITER,
                (array(__CLASS__, $this->key->getFixedPart()))
            );
        }
        return $this->timePartKey;
    }

    private function setNewTimePartOnDependencies()
    {
        foreach($this->getKeyDependencies() as $key) {
            $this->setNewTimePartOnOneDependentKey($key);
        }
    }

    private function setNewTimePartOnOneDependentKey(\G4\RussianDoll\Key $key)
    {
        $digestor = new self($key, $this->mcache);
        $digestor
            ->setNewTimePart();
    }
}