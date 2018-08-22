<?php
namespace Snake\Tests\Extractor;

use PHPUnit\Framework\TestCase;
use Snake\Exception\CannotExtractException;
use Snake\Extractor\CustomExtractor;
use Snake\Tests\Objects\Person;
use Snake\Tests\Objects\PersonWithBirthDate;
use Snake\Tests\Objects\Table;

class CustomExtractorTest extends TestCase
{
  public function testConstructor()
  {
    $customExtractor = new CustomExtractor();

    $this->assertInstanceOf(CustomExtractor::class,$customExtractor);

    return $customExtractor;
  }

  /**
  * @depends testConstructor
  */
  public function testExtract(CustomExtractor $customExtractor)
  {
    $object = new PersonWithBirthDate();
    $object->firstName = 'John';
    $object->lastName = 'Doe';
    $object->gender = 'male';
    $object->birthDate = new \DateTime('1970-01-01');
    $array = $customExtractor->extract($object);

    $this->assertEquals('John',$array['firstName'],'firstName');
    $this->assertEquals('Doe',$array['lastName'],'lastName');
    $this->assertEquals('male',$array['gender'],'gender');
    $this->assertEquals('1970-01-01',$array['birthDate'],'birthDate');
  }

  /**
  * @depends testConstructor
  */
  public function testCannotExtract(CustomExtractor $customExtractor)
  {
    $this->expectException(CannotExtractException::class);

    $object = new Person();
    $object->firstName = 'John';
    $object->lastName = 'Doe';
    $object->gender = 'male';
    $array = $customExtractor->extract($object);
  }

  /**
  * @depends testConstructor
  */
  public function testContextExtract(CustomExtractor $customExtractor)
  {
    $customExtractor->setContext(['prefix' => 'wiki_']);

    $this->assertAttributeEquals(['prefix' => 'wiki_'],'context',$customExtractor,'context');

    $object = new Table();
    $object->name = 'wiki_users';
    $array = $customExtractor->extract($object);

    $this->assertEquals('users',$array['name'],'string');
  }
}
