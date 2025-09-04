<?php

namespace App\Domain\State;

use Exception;
use ReflectionClass;
use ReflectionEnum;
use ReflectionProperty;

trait Stateful
{
    private static array $stateProperties;

    final private function __construct() {}

    /**
     * @throws Exception
     */
    final public function __get(string $name): mixed
    {
        if (isset($this->{$name}) && $this->{$name} instanceof State) {
            return $this->{$name};
        }

        throw new Exception("Property $name is not a accessible.");
    }

    final public function __call(string $name, array $arguments): State
    {
        $reflection = new ReflectionClass($this);

        static::$stateProperties ??= array_filter(
            $reflection->getProperties(),
            fn (ReflectionProperty $property) => is_subclass_of($property->getType()->getName(), State::class)
        );

        if (empty(static::$stateProperties)) {
            throw new InvalidTransitionException('No state properties found in '.static::class);
        }

        foreach (static::$stateProperties as $property) {
            $state = $this->{$property->getName()};

            $stateClassReflection = new ReflectionEnum($state);
            $stateCase = $stateClassReflection->getCase($state->name);
            $stateAttributes = $stateCase->getAttributes(TransitionCollection::class);

            if (empty($stateAttributes)) {
                continue;
            }

            if (count($stateAttributes) > 1) {
                throw new InvalidTransitionException(
                    'Multiple TransitionCollection attributes found in '.static::class
                );
            }

            /** @var TransitionCollection $allowedTransitions */
            $allowedTransitions = reset($stateAttributes)->newInstance();
            $transition = $allowedTransitions->getByName($name);
            if ($transition === null) {
                continue;
            }

            $availableTransitions[$property->getName()] = $transition;
        }

        if (empty($availableTransitions)) {
            throw new InvalidTransitionException(
                "The $name transition is not available for ".static::class
            );
        }

        if (count($availableTransitions) > 1) {
            throw new InvalidTransitionException(
                "Multiple available transitions with name $name found in ".static::class
            );
        }

        $propertyName = key($availableTransitions);
        /** @var Transition $transition */
        $transition = reset($availableTransitions);

        return $this->{$propertyName} = $transition->targetState;
    }
}
