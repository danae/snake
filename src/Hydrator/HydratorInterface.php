<?php
namespace Snake\Hydrator;

interface HydratorInterface
{
  // Convert an array to a hydrated object
  public function hydrate(array $array, string $objectClass, array ...$objectArguents): object;
}
