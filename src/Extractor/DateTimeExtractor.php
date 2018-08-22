<?php
namespace Snake\Extractor;

use Snake\Exception\CannotExtractException;

class DateTimeExtractor implements ExtractorInterface
{
  // Variables
  private $format;

  // Constructor
  public function __construct(string $format = null)
  {
    $this->format = $format;
  }

  // Convert an object to a PHP value
  public function extract(object $object)
  {
    // Check if the class can be extracted
    if (!(is_a($object,\DateTime::class) || is_a($object,\DateTimeImmutable::class)))
      throw new CannotExtractException(get_class($object),self::class);

    // Extract the object
    return $object->format($this->format ?? \DateTime::ISO8601);
  }
}
