<?php
declare(strict_types=1);

namespace Will1471\JsonExtract;

final class Collector
{

    private $objects = [];
    private $arrays = [];

    public function collectArray(string $array): void
    {
        $this->arrays[] = $array;
    }

    public function collectObject(string $object): void
    {
        $this->objects[] = $object;
    }

    public function arrayAtIndex(int $index):?string
    {
        if (isset($this->arrays[$index])) {
            return $this->arrays[$index];
        }
        return null;
    }

    public function objectAtIndex(int $index):?string
    {
        if (isset($this->objects[$index])) {
            return $this->objects[$index];
        }
        return null;
    }
}
