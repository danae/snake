<?php
namespace Snake\Tests\Objects;

use Snake\HydrateableInterface;
use Snake\Hydrator\HydratorInterface;

class Table implements HydrateableInterface
{
  public $name;

  public function hydrate(HydratorInterface $hydrator, array $array, array $context): object
  {
    $this->name = $context['prefix'] . $array['name'];
    return $this;
  }
}
