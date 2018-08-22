<?php
namespace Snake\Extractor;

interface CustomExtractInterface
{
  // Convert an object of this type to an extracted array
  public function extract(ExtractorInterface $extractor, array $context);
}
