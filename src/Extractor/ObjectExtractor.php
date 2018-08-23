<?php
namespace Snake\Extractor;

use Snake\Common\ContextTrait;
use Snake\Common\NameCallbackTrait;
use Snake\Common\NameConvertorTrait;
use Snake\Common\TypeCallbackTrait;
use Snake\Exception\CannotExtractException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\Exception\AccessException;

class ObjectExtractor implements ExtractorInterface
{
  use ContextTrait, TypeCallbackTrait, NameCallbackTrait, NameConvertorTrait, ExtractorMiddlewareTrait;

  // Variables
  private $propertyAccessor;

  // Constructor
  public function __construct(PropertyAccessorInterface $propertyAccessor = null)
  {
    $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
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

    // Apply before middleware
    $object = $this->applyBefore($object,$this->context);

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

      // Apply type callbacks
      $value = $this->applyTypeCallbacks($value,$this->context);

      // Apply name callbacks
      $value = $this->applyNameCallbacks($name,$value,$this->context);

      // Apply name convertors
      $name = $this->applyNameConvertors($name);

      // Add the property to the array
      $array[$name] = $value;
    }

    // Apply after middleware
    $array = $this->applyAfter($array,$this->context);

    // Return the array
    return $array;
  }
}
