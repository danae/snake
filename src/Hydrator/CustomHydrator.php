<?php
namespace Snake\Hydrator;

use Snake\HydrateableInterface;
use Snake\Exception\CannotHydrateException;

class CustomHydrator implements HydratorInterface
{
  // Convert an array to an object
  public function hydrate(array $array, string $objectClass, array ...$objectArguments): object
  {
    // Check if the class can be custom hydrated
    if (!array_key_exists(HydrateableInterface::class,class_implements($objectClass)))
      throw new CannotHydrateException($objectClass,get_class($this));

    // Create a new instance of the object
    $object = new $objectClass(...$objectArguments);

    // Hydrate the object
    return $object->hydrate($array,$this);
  }
}
