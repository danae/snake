<?php
namespace Snake\Tests\Hydrator;

use PHPUnit\Framework\TestCase;
use Snake\Exception\PropertyNotWritableException;
use Snake\Hydrator\ObjectHydrator;
use Snake\Tests\Objects\Person;
use Snake\Tests\Objects\PersonWithBirthDate;

class ObjectHydratorTest extends TestCase
{
  public function testConstructor()
  {
    $objectHydrator = new ObjectHydrator();

    $this->assertInstanceOf(ObjectHydrator::class,$objectHydrator);

    return $objectHydrator;
  }

  /**
  * @depends testConstructor
  */
  public function testHydrate(ObjectHydrator $objectHydrator)
  {
    $array = ['firstName' => 'John', 'lastName' => 'Doe', 'gender' => 'male'];
    $object = $objectHydrator->hydrate($array,Person::class);

    $this->assertInstanceOf(Person::class,$object);
    $this->assertAttributeEquals('John','firstName',$object,'firstName');
    $this->assertAttributeEquals('Doe','lastName',$object,'lastName');
    $this->assertAttributeEquals('male','gender',$object,'gender');
  }

  /**
  * @depends testConstructor
  */
  public function testNotWritableProperty(ObjectHydrator $objectHydrator)
  {
    $this->expectException(PropertyNotWritableException::class);

    $array = ['firstName' => 'John', 'lastName' => 'Doe', 'gender' => 'male', 'city' => 'New York'];
    $object = $objectHydrator->hydrate($array,Person::class);
  }

  /**
  * @depends testConstructor
  */
  public function testTypeConvertingCallback(ObjectHydrator $objectHydrator)
  {
    $objectHydrator->setCallbacks(['birthDate' => function($string) {
      return new \DateTime($string);
    }]);

    $array = ['firstName' => 'John', 'lastName' => 'Doe', 'gender' => 'male', 'birthDate' => '1970-01-01'];
    $object = $objectHydrator->hydrate($array,PersonWithBirthDate::class);

    $this->assertInstanceOf(PersonWithBirthDate::class,$object);
    $this->assertAttributeEquals(new \DateTime('1970-01-01'),'birthDate',$object,'birthDate');
  }

  /**
  * @depends testConstructor
  */
  public function testNameConverterCallback(ObjectHydrator $objectHydrator)
  {
    $objectHydrator->setCallbacks(['name' => function($name) {
      return ['lastName' => $name];
    }]);

    $array = ['name' => 'Doe', 'firstName' => 'John', 'gender' => 'male'];
    $object = $objectHydrator->hydrate($array,Person::class);

    $this->assertInstanceOf(Person::class,$object);
    $this->assertAttributeEquals('John','firstName',$object,'firstName');
    $this->assertAttributeEquals('Doe','lastName',$object,'lastName');
  }

  /**
  * @depends testConstructor
  */
  public function testMultiplePropertiesCallback(ObjectHydrator $objectHydrator)
  {
    $objectHydrator->setCallbacks(['name' => function($name) {
      [$firstName, $lastName] = explode(' ',$name,2);
      return ['firstName' => $firstName, 'lastName' => $lastName];
    }]);

    $array = ['name' => 'John Doe', 'gender' => 'male'];
    $object = $objectHydrator->hydrate($array,Person::class);

    $this->assertInstanceOf(Person::class,$object);
    $this->assertAttributeEquals('John','firstName',$object,'firstName');
    $this->assertAttributeEquals('Doe','lastName',$object,'lastName');
  }
}
