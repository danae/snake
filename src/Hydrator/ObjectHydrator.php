<?php
namespace Snake\Hydrator;

use Snake\Common\ContextTrait;
use Snake\Common\NameCallbackTrait;
use Snake\Common\NameConvertorTrait;
use Snake\Exception\CannotHydrateException;
use Snake\Exception\PropertyNotWritableException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\Exception\AccessException;

class ObjectHydrator implements HydratorInterface
{
  use ContextTrait, NameCallbackTrait, NameConvertorTrait, HydratorMiddlewareTrait;

  // Variables
  private $propertyAccessor;
  private $errorOnNotWritable = true;

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

  // Convert an array to a hydrated object
  public function hydrate(array $array, string $objectClass, array ...$objectArguments): object
  {
    // Apply before middleware
    $array = $this->applyBefore($array,$this->context);

    // Create a new instance of the object
    $object = new $objectClass(...$objectArguments);

    // Iterate over the array
    foreach ($array as $name => $value)
    {
      // Apply name callbacks
      $value = $this->applyNameCallbacks($name,$value,$this->context);

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
    $object = $this->applyAfter($object,$this->context);

    // Return the object
    return $object;
  }
}
