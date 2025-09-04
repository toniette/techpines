<?php

declare(strict_types=1);

namespace App\Domain\Collection;

use App\Domain\Exception\InvalidItemTypeException;
use SplObjectStorage;

abstract class Collection extends SplObjectStorage
{
    abstract protected ?string $type {
        get;
        set;
    }

    public function __construct(object ...$objects)
    {
        $this->attachAll(...$objects);
    }

    public function attachAll(object ...$objects): void
    {
        foreach ($objects as $object) {
            $this->attach($object);
        }
    }

    public function attach(object $object, mixed $info = null): void
    {
        if (! $object instanceof $this->type) {
            throw new InvalidItemTypeException(
                "Object must be an instance of $this->type, ".$object::class.' given'
            );
        }

        parent::attach($object, $info);
    }

    public static function from(object ...$objects): static
    {
        return new static(...$objects);
    }

    public function offsetSet($object, $info = null): void
    {
        $this->attach($object, $info);
    }

    public function addAll(SplObjectStorage $storage): int
    {
        foreach ($storage as $object) {
            $this->attach($object, $storage[$object]);
        }

        return count($storage);
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function toArray(): array
    {
        return iterator_to_array($this);
    }
}
