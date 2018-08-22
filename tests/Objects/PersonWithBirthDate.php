<?php
namespace Snake\Tests\Objects;

use Snake\Extractor\CustomExtractInterface;
use Snake\Extractor\ExtractorInterface;
use Snake\Hydrator\CustomHydrateInterface;
use Snake\Hydrator\HydratorInterface;

class PersonWithBirthDate extends Person implements CustomExtractInterface, CustomHydrateInterface
{
  public $birthDate;

  public function extract(ExtractorInterface $extractor, array $context)
  {
    return [
      'firstName' => $this->firstName,
      'lastName' => $this->lastName,
      'gender' => $this->gender,
      'birthDate' => $this->birthDate->format('Y-m-d')
    ];
  }

  public function hydrate(HydratorInterface $hydrator, $data, array $context): object
  {
    $this->firstName = $data['firstName'];
    $this->lastName = $data['lastName'];
    $this->gender = $data['gender'];
    $this->birthDate = new \DateTime($data['birthDate']);
    return $this;
  }
}
