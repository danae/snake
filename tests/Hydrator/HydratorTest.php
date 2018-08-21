<?php
namespace Snake\Tests\Hydrator;

use PHPUnit\Framework\TestCase;
use Snake\Exception\CannotHydrateException;
use Snake\Hydrator\CustomHydrator;
use Snake\Hydrator\Hydrator;
use Snake\Hydrator\ObjectHydrator;
use Snake\Tests\Objects\Person;
use Snake\Tests\Objects\PersonWithBirthDate;

class HydratorTest extends TestCase
{
  public function testConstructor()
  {
    $customHydrator = new CustomHydrator();
    $objectHydrator = new ObjectHydrator();
    $hydrator = new Hydrator([$customHydrator,$objectHydrator]);

    $this->assertInstanceOf(Hydrator::class,$hydrator);
    $this->assertAttributeEquals([$customHydrator,$objectHydrator],'hydrators',$hydrator,'hydrators');

    return $hydrator;
  }

  public function testInvalidArgumentsConstructor()
  {
    $this->expectException(\InvalidArgumentException::class);

    $customHydrator = new CustomHydrator();
    $stdClass = new \stdClass();
    $hydrator = new Hydrator([$customHydrator,$stdClass]);
  }

  /**
  * @depends testConstructor
  */
  public function testHydrateCustom(Hydrator $hydrator)
  {
    $array = ['firstName' => 'John', 'lastName' => 'Doe', 'gender' => 'male', 'birthDate' => '1970-01-01'];
    $object = $hydrator->hydrate($array,PersonWithBirthDate::class);

    $this->assertInstanceOf(Person::class,$object);
    $this->assertAttributeEquals('John','firstName',$object,'firstName');
    $this->assertAttributeEquals('Doe','lastName',$object,'lastName');
    $this->assertAttributeEquals('male','gender',$object,'gender');
    $this->assertAttributeEquals(new \DateTime('1970-01-01'),'birthDate',$object,'birthDate');
  }

  /**
  * @depends testConstructor
  */
  public function testHydrateObject(Hydrator $hydrator)
  {
    $array = ['firstName' => 'John', 'lastName' => 'Doe', 'gender' => 'male'];
    $object = $hydrator->hydrate($array,Person::class);

    $this->assertInstanceOf(Person::class,$object);
    $this->assertAttributeEquals('John','firstName',$object,'firstName');
    $this->assertAttributeEquals('Doe','lastName',$object,'lastName');
    $this->assertAttributeEquals('male','gender',$object,'gender');
  }
}
