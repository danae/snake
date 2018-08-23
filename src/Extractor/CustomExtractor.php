<?php
namespace Snake\Extractor;

use Snake\Common\ContextTrait;
use Snake\Exception\CannotExtractException;

class CustomExtractor implements ExtractorInterface
{
  use ContextTrait, ExtractorMiddlewareTrait;

  // Convert an object to an extracted array
  public function extract(object $object, ExtractorInterface $extractor = null): array
  {
    $extractor = $extractor ?? $this;

    // Check if the class can be custom extracted
    if (!is_a($object,CustomExtractInterface::class))
      throw new CannotExtractException(get_class($object),self::class);

    // Apply before middleware
    $object = $this->applyBefore($object);

    // Extract the object
    $array = $object->extract($extractor,$this->context);

    // Apply after middleware
    $array = $this->applyAfter($array);

    // Return the array
    return $array;
  }
}
