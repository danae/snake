<?php
namespace Snake\Tests\Hydrator;

use PHPUnit\Framework\TestCase;
use Snake\Exception\CannotHydrateException;
use Snake\Hydrator\CustomHydrator;
use Snake\Tests\Objects\Person;
use Snake\Tests\Objects\PersonWithBirthDate;

class CustomHydratorTest extends TestCase
{
  public function testConstructor()
  {
    $customHydrator = new CustomHydrator();

    $this->assertInstanceOf(CustomHydrator::class,$customHydrator);

    return $customHydrator;
  }

  /**
  * @depends testConstructor
  */
  public function testHydrate(CustomHydrator $customHydrator)
  {
    $array = ['firstName' => 'John', 'lastName' => 'Doe', 'gender' => 'male', 'birthDate' => '1970-01-01'];
    $object = $customHydrator->hydrate($array,PersonWithBirthDate::class);

    $this->assertInstanceOf(Person::class,$object);
    $this->assertAttributeEquals('John','firstName',$object,'firstName');
    $this->assertAttributeEquals('Doe','lastName',$object,'lastName');
    $this->assertAttributeEquals('male','gender',$object,'gender');
    $this->assertAttributeEquals(new \DateTime('1970-01-01'),'birthDate',$object,'birthDate');
  }

  /**
  * @depends testConstructor
  */
  public function testCannotHydrate(CustomHydrator $customHydrator)
  {
    $this->expectException(CannotHydrateException::class);

    $array = ['firstName' => 'John', 'lastName' => 'Doe', 'gender' => 'male'];
    $object = $customHydrator->hydrate($array,Person::class);
  }
}
