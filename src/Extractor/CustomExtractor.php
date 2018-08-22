<?php
namespace Snake\Extractor;

use Snake\Exception\CannotExtractException;

class CustomExtractor implements ExtractorInterface
{
  // Variables
  private $context;

  // Constructor
  public function __construct()
  {
    $this->context = [];
  }

  // Set the context
  public function setContext(array $context): self
  {
    $this->context = $context;
    return $this;
  }

  // Convert an object to a PHP value
  public function extract(object $object)
  {
    // Check if the class can be custom extracted
    if (!is_a($object,CustomExtractInterface::class))
      throw new CannotExtractException(get_class($object),self::class);

    // Extract the object
    return $object->extract($this,$this->context);
  }
}
