<?php
namespace Snake\Extractor;

use Snake\Common\ContextTrait;
use Snake\Exception\CannotExtractException;

class ChainExtractor implements ExtractorInterface
{
  use ContextTrait, ExtractorMiddlewareTrait;

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

  // Set the context for all child extractors
  public function setContext(array $context): self
  {
    foreach ($this->extractors as $extractor)
      $extractor->setContext($context);

    return parent::setContext($context);
  }

  // Convert an object to an extracted array
  public function extract(object $object, ExtractorInterface $extractor = null): array
  {
    $extractor = $extractor ?? $this;

    // Apply before middleware
    $object = $this->applyBefore($object,$this->context);

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
      $array = $this->applyAfter($array,$this->context);

    // Return the array
    return $array;
  }
}
