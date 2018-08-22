<?php
namespace Snake\Hydrator;

use Snake\Exception\CannotHydrateException;

class ChainHydrator implements HydratorInterface
{
  // Variables
  private $hydrators;

  // Constructor
  public function __construct(array $hydrators)
  {
    // Check if all hydrators are hydrators
    foreach ($hydrators as $hydrator)
      if (!is_a($hydrator,HydratorInterface::class))
        throw new \InvalidArgumentException("All arguments must be an instance of " . HydratorInterface::class);

    $this->hydrators = $hydrators;
  }

  // Convert an array to an object
  public function hydrate(array $array, string $objectClass, array ...$objectArguments): object
  {
    // Iterate over the hydrators, skip if cannot hydrate
    foreach ($this->hydrators as $hydrator)
    {
      try
      {
        return $hydrator->hydrate($array,$objectClass,...$objectArguments);
      }
      catch (CannotHydrateException $ex)
      {
        continue;
      }
    }

    // If no hydrator can hydrate the object, then throw it out
    throw new CannotHydrateException($objectClass,get_class($this));
  }
}
