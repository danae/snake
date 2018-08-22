<?php
namespace Snake\Hydrator;

use Snake\Exception\CannotHydrateException;
use Snake\Exception\PropertyNotWritableException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\Exception\AccessException;

class ObjectHydrator implements HydratorInterface
{
  // Variables
  private $propertyAccessor;
  private $errorOnNotWritable;
  private $callbacks;

  // Constructor
  public function __construct(PropertyAccessorInterface $propertyAccessor = null)
  {
    $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
    $this->errorOnNotWritable = true;
    $this->callbacks = [];
  }

  // Set if an error is thrown if a property is not writable
  public function setErrorOnNotWritable(bool $errorOnNotWritable): self
  {
    $this->errorOnNotWritable = $errorOnNotWritable;
    return $this;
  }

  // Set the callbacks
  public function setCallbacks(array $callbacks): self
  {
    $this->callbacks = $callbacks;
    return $this;
  }

  // Convert a PHP value to an object
  public function hydrate($data, string $objectClass, array ...$objectArguments): object
  {
    // Check if the data is an array
    if (!is_array($data))
      throw new CannotHydrateException($objectClass,self::class);

    // Create a new instance of the object
    $object = new $objectClass(...$objectArguments);

    // Iterate over the array
    foreach ($data as $key => $value)
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
      foreach ($properties as $property => $value)
      {
        if ($this->propertyAccessor->isWritable($object,$property))
          $this->propertyAccessor->setValue($object,$property,$value);
        else
        {
          if ($this->errorOnNotWritable)
            throw new PropertyNotWritableException(get_class($object),$property);
          else
            continue;
        }
      }
    }

    // Return the object
    return $object;
  }
}
