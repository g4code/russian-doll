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

    public function __construct(Key $key, Mcache $mcache)
    {
        $this->key         = $key;
        $this->mcache      = $mcache;
        $this->timePartKey = null;
    }

    public function digest()
    {
        return join(self::DELIMITER, $this->getComponentsForDigest());
    }

    public function expire()
    {
        $this->key->hasVariableParts()
            ? $this->setNewTimePart()
            : $this->delete();
        $this->expireDependencies();
    }

    private function delete()
    {
        $this->mcache
            ->id($this->getTimePartKey())
            ->delete();
    }

    private function setNewTimePart()
    {
        $this->timePart = microtime();

        $this->mcache
            ->id($this->getTimePartKey())
            ->object($this->timePart)
            ->expiration($this->key->getCacheLifetime())
            ->set();
    }

    private function getComponentsForDigest()
    {
        return $this->key->hasVariableParts()
            ? [$this->getTimePartKey(), $this->getVariableParts(), $this->getTimePart()]
            : [$this->getTimePartKey()];
    }

    private function getKeyDependencies()
    {
        $dependencies = $this->key->getBelongsTo();
        return is_array($dependencies)
            ? $dependencies
            : [];
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
                ([__CLASS__, $this->key->getFixedPart()])
            );
        }
        return $this->timePartKey;
    }

    private function expireDependencies()
    {
        foreach($this->getKeyDependencies() as $key) {
            $this->expireOneDependentKey($key);
        }
    }

    private function expireOneDependentKey(Key $key)
    {
        (new self($key, $this->mcache))->expire();
    }
}