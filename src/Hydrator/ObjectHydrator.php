<?php
namespace Snake\Hydrator;

use Snake\Exception\PropertyNotWritableException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\Exception\AccessException;

class ObjectHydrator implements HydratorInterface
{
  // TODO: Add support for custom object hydrators and extractors

  // Variables
  private $propertyAccessor;
  private $callbacks;

  // Constructor
  public function __construct(PropertyAccessorInterface $propertyAccessor = null)
  {
    $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
    $this->callbacks = [];
  }

  // Set the callbacks
  public function setCallbacks($callbacks): self
  {
    $this->callbacks = $callbacks;
    return $this;
  }

  // Convert an array to an object
  public function hydrate(array $array, string $objectClass, array ...$objectArguments): object
  {
    // Create a new instance of the object
    $object = new $objectClass(...$objectArguments);

    // Iterate over the array
    foreach ($array as $key => $value)
    {
      // Check if there is a callback for this key
      if (array_key_exists($key,$this->callbacks))
      {
        // Execute the callbacks and store its result in an array
        $properties = $this->callbacks[$key]($value);

        // If properties is no array, then convert it to an array
        if (!is_array($properties))
          $properties = [$key => $properties];
      }
      else
      {
        // Otherwise wrap the key and value in an array
        $properties = [$key => $value];
      }

      // Set the properties
      foreach ($properties as $k => $v)
      {
        if ($this->propertyAccessor->isWritable($object,$k))
          $this->propertyAccessor->setValue($object,$k,$v);
        else
          throw new PropertyNotWritableException(get_class($object),$k);
      }
    }

    // Return the object
    return $object;
  }
}
