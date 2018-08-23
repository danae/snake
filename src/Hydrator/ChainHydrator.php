<?php
namespace Snake\Hydrator;

use Snake\Common\ContextTrait;
use Snake\Exception\CannotHydrateException;

class ChainHydrator implements HydratorInterface
{
  use ContextTrait, HydratorMiddlewareTrait;

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

  // Set the context for all child hydrators
  public function setContext(array $context): self
  {
    foreach ($this->hydrators as $hydrator)
      $hydrator->setContext($context);
      
    return parent::setContext($context);
  }

  // Convert an array to a hydrated object
  public function hydrate(array $array, string $objectClass, array ...$objectArguments): object
  {
    // Apply before middleware
    $array = $this->applyBefore($array,$this->context);

    // Create an empty object reference
    $object = null;

    // Iterate over the hydrators, skip if cannot hydrate
    foreach ($this->hydrators as $hydrator)
    {
      try
      {
        $object = $hydrator->hydrate($array,$objectClass,...$objectArguments);
        break;
      }
      catch (CannotHydrateException $ex)
      {
        continue;
      }
    }

    // Check if the array is hydrated
    if ($object === null)
      throw new CannotHydrateException($objectClass,self::class);

    // Apply after middleware
    $object = $this->applyAfter($object,$this->context);

    // Return the object
    return $object;
  }
}
