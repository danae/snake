<?php
namespace Snake;

use Snake\Hydrator\HydratorInterface;

interface HydrateableInterface
{
  // Convert an array to an object of this type
  public function hydrate(HydratorInterface $hydrator, array $array, array $context): object;
}
