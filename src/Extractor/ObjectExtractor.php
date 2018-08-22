<?php
namespace Snake\Extractor;

use Snake\Exception\CannotExtractException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\Exception\AccessException;

class ObjectExtractor implements ExtractorInterface
{
  // Variables
  private $propertyAccessor;
  private $typeCallbacks;
  private $nameCallbacks;
  private $nameConverters;

  // Constructor
  public function __construct(PropertyAccessorInterface $propertyAccessor = null)
  {
    $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
    $this->typeCallbacks = [];
    $this->nameCallbacks = [];
    $this->nameConverters = [];
  }

  // Set the type callbacks
  public function setTypeCallbacks(array $typeCallbacks): self
  {
    foreach ($typeCallbacks as $type => $callback)
      if (!is_string($type) || !is_callable($callback))
        throw new \InvalidArgumentException("Type callbacks must be an associative-only array containing callables");

    $this->typeCallbacks = $typeCallbacks;
    return $this;
  }

  // Set the name callbacks
  public function setNameCallbacks(array $nameCallbacks): self
  {
    foreach ($nameCallbacks as $name => $callback)
      if (!is_string($name) || !is_callable($callback))
        throw new \InvalidArgumentException("Name callbacks must be an associative-only array containing callables");

    $this->nameCallbacks = $nameCallbacks;
    return $this;
  }

  // Set the name converters
  public function setNameConverters(array $nameConverters): self
  {
    foreach ($nameConverters as $name => $mappedName)
      if (!is_string($name) || !is_string($mappedName))
        throw new \InvalidArgumentException("Name converters must be an associative-only array containing strings");

    $this->nameConverters = $nameConverters;
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

  // Convert an object to an extracted array
  public function extract(object $object, ExtractorInterface $extractor = null): array
  {
    $extractor = $extractor ?? $this;

    // Create a new array
    $array = [];

    // Iterate over the available properties
    foreach ($this->getProperties($object) as $name)
    {
      // Check if the property is readable
      if (!$this->propertyAccessor->isReadable($object,$name))
        continue;

      // Read the property
      $value = $this->propertyAccessor->getValue($object,$name);
      $type = is_object($value) ? get_class($value) : gettype($value);

      // Check if there is a type callback for this property
      if (array_key_exists($type,$this->typeCallbacks))
        $value = $this->typeCallbacks[$type]($value);

      // Check if there is a name callback for this property
      if (array_key_exists($name,$this->nameCallbacks))
        $value = $this->nameCallbacks[$name]($value);

      // Check if there is a name converter for this property
      if (array_key_exists($name,$this->nameConverters))
        $name = $this->nameConverters[$name];

      // Add the property to the array
      $array[$name] = $value;
    }

    // Return the array
    return $array;
  }
}
