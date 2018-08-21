<?php
namespace Snake\Tests\Objects;

use Snake\HydrateableInterface;
use Snake\Hydrator\HydratorInterface;

class PersonWithBirthDate extends Person implements HydrateableInterface
{
  public $birthDate;

  public function hydrate(array $array, HydratorInterface $hydrator): object
  {
    $this->firstName = $array['firstName'];
    $this->lastName = $array['lastName'];
    $this->gender = $array['gender'];
    $this->birthDate = new \DateTime($array['birthDate']);
    return $this;
  }
}
