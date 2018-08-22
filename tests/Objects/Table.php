<?php
namespace Snake\Tests\Objects;

use Snake\Extractor\CustomExtractInterface;
use Snake\Extractor\ExtractorInterface;
use Snake\Hydrator\CustomHydrateInterface;
use Snake\Hydrator\HydratorInterface;

class Table implements CustomExtractInterface, CustomHydrateInterface
{
  public $name;

  public function extract(ExtractorInterface $extractor, array $context)
  {
    return strpos($this->name,$context['prefix']) === 0 ? substr($this->name,strlen($context['prefix'])) : $this->name;
  }

  public function hydrate(HydratorInterface $hydrator, $data, array $context): object
  {
    $this->name = $context['prefix'] . $data;
    return $this;
  }
}
