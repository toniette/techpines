<?php

namespace App\Domain\State;

use BackedEnum;

final class Transition
{
    public function __construct(
        public string           $name,
        public State&BackedEnum $targetState
    ) {}
}
