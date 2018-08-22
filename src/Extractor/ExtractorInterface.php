<?php
namespace Snake\Extractor;

interface ExtractorInterface
{
  // Convert an object to an extracted array
  public function extract(object $object, ExtractorInterface $extractor = null): array;
}
