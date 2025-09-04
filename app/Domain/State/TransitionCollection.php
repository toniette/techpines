<?php

namespace App\Domain\State;

use App\Domain\Collection\Collection;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class TransitionCollection extends Collection
{
    protected ?string $type = Transition::class;

    public function getByName(string $name): ?Transition
    {
        return array_find(iterator_to_array($this), fn (Transition $transition) => $transition->name === $name);
    }
}
