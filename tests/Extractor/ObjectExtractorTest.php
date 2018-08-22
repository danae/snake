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
  public function testTypeCallback(ObjectExtractor $objectExtractor)
  {
    $objectExtractor->setTypeCallbacks([\DateTime::class => function($dateTime) {
      return $dateTime->format('Y-m-d');
    }]);
    $objectExtractor->setNameCallbacks([]);
    $objectExtractor->setNameConvertors([]);
    $objectExtractor->setBefore([]);
    $objectExtractor->setAfter([]);

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
  public function testNameCallback(ObjectExtractor $objectExtractor)
  {
    $objectExtractor->setTypeCallbacks([]);
    $objectExtractor->setNameCallbacks(['birthDate' => function($dateTime) {
      return $dateTime->format('Y-m-d');
    }]);
    $objectExtractor->setNameConvertors([]);
    $objectExtractor->setBefore([]);
    $objectExtractor->setAfter([]);

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
  public function testNameConverter(ObjectExtractor $objectExtractor)
  {
    $objectExtractor->setTypeCallbacks([]);
    $objectExtractor->setNameCallbacks([]);
    $objectExtractor->setNameConvertors(['lastName' => 'name']);
    $objectExtractor->setBefore([]);
    $objectExtractor->setAfter([]);

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
  public function testBeforeMiddleware(ObjectExtractor $objectExtractor)
  {
    $objectExtractor->setTypeCallbacks([]);
    $objectExtractor->setNameCallbacks([]);
    $objectExtractor->setNameConvertors([]);
    $objectExtractor->setBefore([function($object) {
      $object->firstName = 'Jane';
      return $object;
    }]);
    $objectExtractor->setAfter([]);

    $object = new Person();
    $object->firstName = 'John';
    $object->lastName = 'Doe';
    $object->gender = 'male';
    $array = $objectExtractor->extract($object);

    $this->assertEquals('Jane',$array['firstName'],'firstName');
  }

  /**
  * @depends testConstructor
  */
  public function testAfterMiddleware(ObjectExtractor $objectExtractor)
  {
    $objectExtractor->setTypeCallbacks([]);
    $objectExtractor->setNameCallbacks([]);
    $objectExtractor->setNameConvertors([]);
    $objectExtractor->setBefore([]);
    $objectExtractor->setAfter([function($array) {
      $array['name'] = $array['firstName'] . ' ' . $array['lastName'];
      unset($array['firstName']);
      unset($array['lastName']);
      return $array;
    }]);

    $object = new Person();
    $object->firstName = 'John';
    $object->lastName = 'Doe';
    $object->gender = 'male';
    $array = $objectExtractor->extract($object);

    $this->assertEquals('John Doe',$array['name'],'name');
  }
}
