<?php
namespace Snake\Hydrator;

interface CustomHydrateInterface
{
  // Convert an array to an object of this type
  public function hydrate(HydratorInterface $hydrator, array $array, array $context): object;
}
