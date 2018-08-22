<?php
namespace Snake\Tests\Objects;

use Snake\Hydrator\CustomHydrateInterface;
use Snake\Hydrator\HydratorInterface;

class PersonWithBirthDate extends Person implements CustomHydrateInterface
{
  public $birthDate;

  public function hydrate(HydratorInterface $hydrator, array $array, array $context): object
  {
    $this->firstName = $array['firstName'];
    $this->lastName = $array['lastName'];
    $this->gender = $array['gender'];
    $this->birthDate = new \DateTime($array['birthDate']);
    return $this;
  }
}
