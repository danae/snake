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
  public function testNotWritablePropertyWithoutError(ObjectHydrator $objectHydrator)
  {
    $objectHydrator->setErrorOnNotWritable(false);

    $this->assertAttributeEquals(false,'errorOnNotWritable',$objectHydrator,'errorOnNotWritable');

    $array = ['firstName' => 'John', 'lastName' => 'Doe', 'gender' => 'male', 'city' => 'New York'];
    $object = $objectHydrator->hydrate($array,Person::class);

    $this->assertObjectNotHasAttribute('city',$object,'city');
  }

  /**
  * @depends testConstructor
  */
  public function testNameCallback(ObjectHydrator $objectHydrator)
  {
    $objectHydrator->setNameCallbacks(['birthDate' => function($string) {
      return new \DateTime($string);
    }]);
    $objectHydrator->setNameConverters([]);

    $array = ['firstName' => 'John', 'lastName' => 'Doe', 'gender' => 'male', 'birthDate' => '1970-01-01'];
    $object = $objectHydrator->hydrate($array,PersonWithBirthDate::class);

    $this->assertInstanceOf(PersonWithBirthDate::class,$object);
    $this->assertAttributeEquals(new \DateTime('1970-01-01'),'birthDate',$object,'birthDate');
  }

  /**
  * @depends testConstructor
  */
  public function testNameConverter(ObjectHydrator $objectHydrator)
  {
    $objectHydrator->setNameCallbacks([]);
    $objectHydrator->setNameConverters(['name' => 'lastName']);

    $array = ['name' => 'Doe', 'firstName' => 'John', 'gender' => 'male'];
    $object = $objectHydrator->hydrate($array,Person::class);

    $this->assertInstanceOf(Person::class,$object);
    $this->assertAttributeEquals('John','firstName',$object,'firstName');
    $this->assertAttributeEquals('Doe','lastName',$object,'lastName');
  }
}
