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
    return [
      'name' => strpos($this->name,$context['prefix']) === 0 ? substr($this->name,strlen($context['prefix'])) : $this->name
    ];
  }

  public function hydrate(HydratorInterface $hydrator, array $array, array $context): object
  {
    $this->name = $context['prefix'] . $array['name'];
    return $this;
  }
}
