<?php
namespace Snake;

use Snake\Hydrator\HydratorInterface;

interface HydrateableInterface
{
  // Convert an array to an object of this type
  public function hydrate(array $array, HydratorInterface $hydrator): object;
}
