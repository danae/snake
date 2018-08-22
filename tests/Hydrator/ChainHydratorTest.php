<?php
namespace Snake\Tests\Hydrator;

use PHPUnit\Framework\TestCase;
use Snake\Exception\CannotHydrateException;
use Snake\Hydrator\ChainHydrator;
use Snake\Hydrator\CustomHydrator;
use Snake\Hydrator\ObjectHydrator;
use Snake\Tests\Objects\Person;
use Snake\Tests\Objects\PersonWithBirthDate;

class ChainHydratorTest extends TestCase
{
  public function testConstructor()
  {
    $customHydrator = new CustomHydrator();
    $objectHydrator = new ObjectHydrator();
    $chainHydrator = new ChainHydrator([$customHydrator,$objectHydrator]);

    $this->assertInstanceOf(ChainHydrator::class,$chainHydrator);
    $this->assertAttributeEquals([$customHydrator,$objectHydrator],'hydrators',$chainHydrator,'hydrators');

    return $chainHydrator;
  }

  public function testInvalidArgumentsConstructor()
  {
    $this->expectException(\InvalidArgumentException::class);

    $customHydrator = new CustomHydrator();
    $stdClass = new \stdClass();
    $chainHydrator = new ChainHydrator([$customHydrator,$stdClass]);
  }

  /**
  * @depends testConstructor
  */
  public function testHydrateCustom(ChainHydrator $chainHydrator)
  {
    $array = ['firstName' => 'John', 'lastName' => 'Doe', 'gender' => 'male', 'birthDate' => '1970-01-01'];
    $object = $chainHydrator->hydrate($array,PersonWithBirthDate::class);

    $this->assertInstanceOf(Person::class,$object);
    $this->assertAttributeEquals('John','firstName',$object,'firstName');
    $this->assertAttributeEquals('Doe','lastName',$object,'lastName');
    $this->assertAttributeEquals('male','gender',$object,'gender');
    $this->assertAttributeEquals(new \DateTime('1970-01-01'),'birthDate',$object,'birthDate');
  }

  /**
  * @depends testConstructor
  */
  public function testHydrateObject(ChainHydrator $chainHydrator)
  {
    $array = ['firstName' => 'John', 'lastName' => 'Doe', 'gender' => 'male'];
    $object = $chainHydrator->hydrate($array,Person::class);

    $this->assertInstanceOf(Person::class,$object);
    $this->assertAttributeEquals('John','firstName',$object,'firstName');
    $this->assertAttributeEquals('Doe','lastName',$object,'lastName');
    $this->assertAttributeEquals('male','gender',$object,'gender');
  }
}
