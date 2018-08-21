<?php
namespace Snake\Exception;

class CannotHydrateException extends \RuntimeException
{
  // Variables
  private $className;
  private $hydratorClassName;

  // Constructor
  public function __construct(string $className, string $hydratorClassName)
  {
    parent::__construct("Object of type '{$className}' cannot be hydrated by hydrator of type '{$hydratorClassName}'");

    $this->className = $className;
    $this->hydratorClassName = $hydratorClassName;
  }

  // Return the class name
  public function getClassName()
  {
    return $this->className;
  }

  // Return the hydrator class name
  public function getHydratorClassName()
  {
    return $this->hydratorClassName;
  }
}
