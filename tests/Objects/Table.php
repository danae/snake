<?php
namespace Snake\Tests\Objects;

use Snake\Hydrator\CustomHydrateInterface;
use Snake\Hydrator\HydratorInterface;

class Table implements CustomHydrateInterface
{
  public $name;

  public function hydrate(HydratorInterface $hydrator, array $array, array $context): object
  {
    $this->name = $context['prefix'] . $array['name'];
    return $this;
  }
}
