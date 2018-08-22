<?php
namespace Snake\Extractor;

use Snake\Exception\CannotExtractException;

class ChainExtractor implements ExtractorInterface
{
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

    // Iterate over the extractors, skip if cannot extract
    foreach ($this->extractors as $extractor)
    {
      try
      {
        return $extractor->extract($object,$extractor);
      }
      catch (CannotExtractException $ex)
      {
        continue;
      }
    }

    // If no extractor can extract the object, then throw it out
    throw new CannotExtractException(get_class($object),self::class);
  }

  // Return if this extractor supports extracting a value
  public function supportsExtraction($value)
  {
    return false;
  }
}
