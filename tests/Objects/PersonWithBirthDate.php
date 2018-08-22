<?php
namespace Snake\Tests\Objects;

use Snake\Extractor\CustomExtractInterface;
use Snake\Extractor\ExtractorInterface;
use Snake\Hydrator\CustomHydrateInterface;
use Snake\Hydrator\HydratorInterface;

class PersonWithBirthDate extends Person implements CustomExtractInterface, CustomHydrateInterface
{
  public $birthDate;

  public function extract(ExtractorInterface $extractor, array $context): array
  {
    return [
      'firstName' => $this->firstName,
      'lastName' => $this->lastName,
      'gender' => $this->gender,
      'birthDate' => $this->birthDate->format('Y-m-d')
    ];
  }

  public function hydrate(HydratorInterface $hydrator, array $array, array $context): object
  {
    $this->firstName = $array['firstName'];
    $this->lastName = $array['lastName'];
    $this->gender = $array['gender'];
    $this->birthDate = new \DateTime($array['birthDate']);
    return $this;
  }
}
