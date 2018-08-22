<?php
namespace Snake\Exception;

class CannotExtractException extends \RuntimeException
{
  // Variables
  private $className;
  private $extractorClassName;

  // Constructor
  public function __construct(string $className, string $extractorClassName)
  {
    parent::__construct("Object of type '{$className}' cannot be extracted by extractor of type '{$extractorClassName}'");

    $this->className = $className;
    $this->extractorClassName = $extractorClassName;
  }

  // Return the class name
  public function getClassName()
  {
    return $this->className;
  }

  // Return the extractor class name
  public function getExtractorClassName()
  {
    return $this->extractorClassName;
  }
}
