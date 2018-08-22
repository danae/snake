<?php
namespace Snake\Hydrator;

interface CustomHydrateInterface
{
  // Convert a PHP value to an object of this type
  public function hydrate(HydratorInterface $hydrator, $data, array $context): object;
}
