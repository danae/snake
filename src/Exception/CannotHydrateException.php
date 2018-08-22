<?php
namespace Snake\Exception;

class CannotHydrateException extends \RuntimeException
{
  // Variables
  private $type;
  private $hydratorClass;

  // Constructor
  public function __construct(string $type, string $hydratorClass)
  {
    parent::__construct("{$type} cannot be hydrated by hydrator of type '{$hydratorClass}'");

    $this->type = $type;
    $this->hydratorClass = $hydratorClass;
  }

  // Return the type of the value that can't be hydrated
  public function getType()
  {
    return $this->type;
  }

  // Return the hydrator class name
  public function getHydratorClass()
  {
    return $this->hydratorClass;
  }
}
