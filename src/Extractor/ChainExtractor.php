<?php
namespace Snake\Extractor;

use Snake\Exception\CannotExtractException;

class ChainExtractor implements ExtractorInterface
{
  use ExtractorMiddlewareTrait;

  // Variables
  private $extractors;

  // Constructor
  public function __construct(array $extractors)
  {
    // Check if all extractors are extractors
    foreach ($extractors as $extractor)
      if (!is_a($extractor,ExtractorInterface::class))
        throw new \InvalidArgumentException("All arguments must be an instance of " . ExtractorInterface::class);

    $this->extractors = $extractors;
  }

  // Convert an object to an extracted array
  public function extract(object $object, ExtractorInterface $extractor = null): array
  {
    $extractor = $extractor ?? $this;

    // Apply before middleware
    $object = $this->applyBefore($object);

    // Create an empty array reference
    $array = null;

    // Iterate over the extractors, skip if cannot extract
    foreach ($this->extractors as $extractor)
    {
      try
      {
        $array = $extractor->extract($object,$extractor);
        break;
      }
      catch (CannotExtractException $ex)
      {
        continue;
      }
    }

    // Check if the array is extracted
    if ($array == null)
      throw new CannotExtractException(get_class($object),self::class);

      // Apply after middleware
      $array = $this->applyAfter($array);

    // Return the array
    return $array;
  }
}
