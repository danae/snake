<?php
namespace Snake\Hydrator;

interface HydratorInterface
{
  // Convert an array to an object
  public function hydrate(array $array, string $objectClass, array ...$objectArguents): object;
}
