<?php
namespace Snake\Exception;

class CannotExtractException extends \RuntimeException
{
  // Variables
  private $type;
  private $extractorClass;

  // Constructor
  public function __construct(string $type, string $extractorClass)
  {
    parent::__construct("{$type} cannot be extracted by extractor of type '{$extractorClass}'");

    $this->type = $type;
    $this->extractorClass = $extractorClass;
  }

  // Return the type of the value that can't be extracted
  public function getType()
  {
    return $this->type;
  }

  // Return the extractor class
  public function getExtractorClass()
  {
    return $this->extractorClass;
  }
}
