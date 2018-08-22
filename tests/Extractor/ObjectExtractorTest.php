<?php
namespace Snake\Tests\Extractor;

use PHPUnit\Framework\TestCase;
use Snake\Exception\PropertyNotWritableException;
use Snake\Extractor\ObjectExtractor;
use Snake\Tests\Objects\Person;
use Snake\Tests\Objects\PersonWithBirthDate;

class ObjectExtractorTest extends TestCase
{
  public function testConstructor()
  {
    $objectExtractor = new ObjectExtractor();

    $this->assertInstanceOf(ObjectExtractor::class,$objectExtractor);

    return $objectExtractor;
  }

  /**
  * @depends testConstructor
  */
  public function testExtract(ObjectExtractor $objectExtractor)
  {
    $object = new Person();
    $object->firstName = 'John';
    $object->lastName = 'Doe';
    $object->gender = 'male';
    $array = $objectExtractor->extract($object);

    $this->assertEquals('John',$array['firstName'],'firstName');
    $this->assertEquals('Doe',$array['lastName'],'lastName');
    $this->assertEquals('male',$array['gender'],'gender');
  }

  /**
  * @depends testConstructor
  */
  public function testTypeConvertingCallback(ObjectExtractor $objectExtractor)
  {
    $objectExtractor->setCallbacks(['birthDate' => function($dateTime) {
      return $dateTime->format('Y-m-d');
    }]);

    $object = new PersonWithBirthDate();
    $object->firstName = 'John';
    $object->lastName = 'Doe';
    $object->gender = 'male';
    $object->birthDate = new \DateTime('1970-01-01');
    $array = $objectExtractor->extract($object);

    $this->assertEquals('1970-01-01',$array['birthDate'],'birthDate');
  }

  /**
  * @depends testConstructor
  */
  public function testNameConverterCallback(ObjectExtractor $objectExtractor)
  {
    $objectExtractor->setCallbacks(['lastName' => function($name) {
      return ['name' => $name];
    }]);

    $object = new Person();
    $object->firstName = 'John';
    $object->lastName = 'Doe';
    $object->gender = 'male';
    $array = $objectExtractor->extract($object);

    $this->assertEquals('Doe',$array['name'],'name');
  }

  /**
  * @depends testConstructor
  */
  public function testMultiplePropertiesCallback(ObjectExtractor $objectExtractor)
  {
    $objectExtractor->setCallbacks(['firstName' => function($firstName) {
      return ['firstName' => $firstName, 'initial' => substr($firstName,0,1)];
    }]);

    $object = new Person();
    $object->firstName = 'John';
    $object->lastName = 'Doe';
    $object->gender = 'male';
    $array = $objectExtractor->extract($object);

    $this->assertEquals('J',$array['initial'],'initial');
  }
}
