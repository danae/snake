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
  private $nameCallbacks;
  private $nameConverters;

  // Constructor
  public function __construct(PropertyAccessorInterface $propertyAccessor = null)
  {
    $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
    $this->errorOnNotWritable = true;
    $this->nameCallbacks = [];
    $this->nameConverters = [];
  }

  // Set if an error is thrown if a property is not writable
  public function setErrorOnNotWritable(bool $errorOnNotWritable): self
  {
    $this->errorOnNotWritable = $errorOnNotWritable;
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

  // Convert an array to a hydrated object
  public function hydrate(array $array, string $objectClass, array ...$objectArguments): object
  {
    // Create a new instance of the object
    $object = new $objectClass(...$objectArguments);

    // Iterate over the array
    foreach ($array as $name => $value)
    {
      // Check if there is a name callback for this property
      if (array_key_exists($name,$this->nameCallbacks))
        $value = $this->nameCallbacks[$name]($value);

      // Check if there is a name converter for this property
      if (array_key_exists($name,$this->nameConverters))
        $name = $this->nameConverters[$name];

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

    // Return the object
    return $object;
  }
}
