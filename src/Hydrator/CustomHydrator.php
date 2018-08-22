<?php
namespace Snake\Hydrator;

use Snake\Exception\CannotHydrateException;

class CustomHydrator implements HydratorInterface
{
  // Variables
  private $context;

  // Constructor
  public function __construct()
  {
    $this->context = [];
  }

  // Set the context
  public function setContext(array $context): self
  {
    $this->context = $context;
    return $this;
  }

  // Convert an array to an object
  public function hydrate(array $array, string $objectClass, array ...$objectArguments): object
  {
    // Check if the class can be custom hydrated
    if (!array_key_exists(CustomHydrateInterface::class,class_implements($objectClass)))
      throw new CannotHydrateException($objectClass,get_class($this));

    // Create a new instance of the object
    $object = new $objectClass(...$objectArguments);

    // Hydrate the object
    return $object->hydrate($this,$array,$this->context);
  }
}
