<?php
namespace Snake\Hydrator;

interface CustomHydrateInterface
{
  // Convert an array to a hydrated object of this type
  public function hydrate(HydratorInterface $hydrator, array $array, array $context): object;
}
