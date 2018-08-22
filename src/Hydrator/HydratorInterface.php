<?php
namespace Snake\Hydrator;

interface HydratorInterface
{
  // Convert a PHP value to an object
  public function hydrate($data, string $objectClass, array ...$objectArguents): object;
}
