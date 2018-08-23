<?php
namespace Snake\Hydrator;

use Snake\Common\ContextTrait;
use Snake\Exception\CannotHydrateException;

class CustomHydrator implements HydratorInterface
{
  use ContextTrait, HydratorMiddlewareTrait;

  // Convert an array to a hydrated object
  public function hydrate(array $array, string $objectClass, array ...$objectArguments): object
  {
    // Check if the class can be custom hydrated
    if (!array_key_exists(CustomHydrateInterface::class,class_implements($objectClass)))
      throw new CannotHydrateException($objectClass,self::class);

    // Apply before middleware
    $array = $this->applyBefore($array,$this->context);

    // Create a new instance of the object
    $object = new $objectClass(...$objectArguments);

    // Hydrate the object
    $object = $object->hydrate($this,$array,$this->context);

    // Apply after middleware
    $object = $this->applyAfter($object,$this->context);

    // Return the object
    return $object;
  }
}
