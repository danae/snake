<?php
namespace Snake\Extractor;

interface ExtractorInterface
{
  // Convert an object to a PHP value
  public function extract(object $object);
}
