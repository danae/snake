<?php
namespace Snake\Exception;

class PropertyNotReadableException extends \RuntimeException
{
  // Variables
  private $className;
  private $propertyName;

  // Constructor
  public function __construct(string $className, string $propertyName)
  {
    parent::__construct("Property '{$propertyName}' is not readable in object of type '{$className}'");

    $this->className = $className;
    $this->propertyName = $propertyName;
  }

  // Return the class name
  public function getClassName()
  {
    return $this->className;
  }

  // Return the property name
  public function getPropertyName()
  {
    return $this->propertyName;
  }
}
