<?php
namespace Snake\Extractor;

interface CustomExtractInterface
{
  // Convert an object of this type to a PHP value
  public function extract(ExtractorInterface $extractor, array $context);
}
