<?php
namespace Snake\Hydrator;

use Snake\Common\NameCallbackTrait;
use Snake\Common\NameConvertorTrait;
use Snake\Exception\CannotHydrateException;
use Snake\Exception\PropertyNotWritableException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\Exception\AccessException;

class ObjectHydrator implements HydratorInterface
{
  use NameCallbackTrait, NameConvertorTrait;

  // Variables
  private $propertyAccessor;
  private $errorOnNotWritable = true;
  private $before = [];
  private $after = [];

  // Constructor
  public function __construct(PropertyAccessorInterface $propertyAccessor = null)
  {
    $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
  }

  // Set if an error is thrown if a property is not writable
  public function setErrorOnNotWritable(bool $errorOnNotWritable): self
  {
    $this->errorOnNotWritable = $errorOnNotWritable;
    return $this;
  }

  // Set the before middleware
  public function setBefore(array $before): self
  {
    foreach ($before as $callback)
      if (!is_callable($callback))
        throw new \InvalidArgumentException("Middleware must be an indexed array of callables");

    $this->before = $before;
    return $this;
  }

  // Set the after middlafeware
  public function setAfter(array $after): self
  {
    foreach ($after as $callback)
      if (!is_callable($callback))
        throw new \InvalidArgumentException("Middleware must be an indexed array of callables");

    $this->after = $after;
    return $this;
  }

  // Apply the before middleware
  protected function applyBefore(array $array): array
  {
    foreach ($this->before as $middleware)
      $array = $middleware($array);
    return $array;
  }

  // Apply the after middleware
  protected function applyAfter(object $object): object
  {
    foreach ($this->after as $middleware)
      $object = $middleware($object);
    return $object;
  }

  // Convert an array to a hydrated object
  public function hydrate(array $array, string $objectClass, array ...$objectArguments): object
  {
    // Apply before middleware
    $array = $this->applyBefore($array);

    // Create a new instance of the object
    $object = new $objectClass(...$objectArguments);

    // Iterate over the array
    foreach ($array as $name => $value)
    {
      // Apply name callbacks
      $value = $this->applyNameCallbacks($name,$value);

      // Apply name convertors
      $name = $this->applyNameConvertors($name);

      // Check if the property is writable
      if (!$this->propertyAccessor->isWritable($object,$name))
      {
        if ($this->errorOnNotWritable)
          throw new PropertyNotWritableException(get_class($object),$name);
        else
          continue;
      }

      // Write the property
      $this->propertyAccessor->setValue($object,$name,$value);
    }

    // Apply after middleware
    $object = $this->applyAfter($object);

    // Return the object
    return $object;
  }
}
