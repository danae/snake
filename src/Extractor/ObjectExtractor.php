<?php
namespace Snake\Extractor;

use Snake\Exception\CannotExtractException;
use Snake\Exception\PropertyNotReadableException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\Exception\AccessException;

class ObjectExtractor implements ExtractorInterface
{
  // Variables
  private $propertyAccessor;
  private $errorOnNotReadable;
  private $callbacks;

  // Constructor
  public function __construct(PropertyAccessorInterface $propertyAccessor = null)
  {
    $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
    $this->errorOnNotReadable = true;
    $this->callbacks = [];
  }

  // Set if an error is thrown if a property is not readable
  public function setErrorOnNotReadable(bool $errorOnNotReadable): self
  {
    $this->errorOnNotReadable = $errorOnNotReadable;
    return $this;
  }

  // Set the callbacks
  public function setCallbacks(array $callbacks): self
  {
    $this->callbacks = $callbacks;
    return $this;
  }

  // Return the properties of an object
  // Adapted from the symfony/serializer project
  private function getProperties(object $object)
  {
    $properties = [];
    $rClass = new \ReflectionClass($object);

    // Search for methods
    foreach ($rClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $rMethod)
    {
      // Check if the method is useable
      if ($rMethod->getNumberOfRequiredParameters() !== 0 || $rMethod->isStatic() || $rMethod->isConstructor() || $rMethod->isDestructor())
        continue;

      $name = $rMethod->name;
      $propertyName = null;

      // Check if it's a getter or hasser
      if (strpos($name,'get') === 0 || strpos($name,'has') === 0)
      {
        $propertyName = substr($name,3);
        if (!$rClass->hasProperty($propertyName))
          $propertyName = lcfirst($propertyName);
      }

      // Check if it's a isser
      elseif (strpos($name,'is') === 0)
      {
        $propertyName = substr($name,2);
        if (!$rClass->hasProperty($propertyName))
          $propertyName = lcfirst($propertyName);
      }

      // Check if the method is really valid, then add the method
      if ($propertyName !== null)
        $properties[$propertyName] = true;
    }

    // Search for properties
    foreach ($rClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $rProperty)
    {
      // Check if the property is useable
      if ($rProperty->isStatic())
        continue;

      // Add the property
      $properties[$rProperty->name] = true;
    }

    // Return the properties
    return array_keys($properties);
  }

  // Convert an object to a PHP value
  public function extract(object $object)
  {
    // Create a new array
    $array = [];
    foreach ($this->getProperties($object) as $property)
    {
      // Read the property
      if ($this->propertyAccessor->isReadable($object,$property))
        $array[$property] = $this->propertyAccessor->getValue($object,$property);
      else
      {
        if ($this->errorOnNotReadable)
          throw new PropertyNotReadableException(get_class($object),$key);
        else
          continue;
      }
    }

    // Iterate over the array
    $realArray = [];
    foreach ($array as $key => $value)
    {
      // Check if there is a callback for this key
      if (array_key_exists($key,$this->callbacks))
      {
        // Execute the callbacks and store its result in an array
        $values = $this->callbacks[$key]($value);

        // If values is no array, then convert it to an array
        if (!is_array($values))
          $values = [$key => $values];
      }
      else
      {
        // Otherwise wrap the key and value in an array
        $values = [$key => $value];
      }

      // Add the values to the real array
      $realArray += $values;
    }

    // Return the real array
    return $realArray;
  }
}
