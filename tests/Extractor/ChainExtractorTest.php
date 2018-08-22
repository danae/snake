<?php
namespace Snake\Tests\Extractor;

use PHPUnit\Framework\TestCase;
use Snake\Exception\CannotHydrateException;
use Snake\Extractor\ChainExtractor;
use Snake\Extractor\CustomExtractor;
use Snake\Extractor\ObjectExtractor;
use Snake\Tests\Objects\Person;
use Snake\Tests\Objects\PersonWithBirthDate;

class ChainExtractorTest extends TestCase
{
  public function testConstructor()
  {
    $customExtractor = new CustomExtractor();
    $objectExtractor = new ObjectExtractor();
    $chainExtractor = new ChainExtractor([$customExtractor,$objectExtractor]);

    $this->assertInstanceOf(ChainExtractor::class,$chainExtractor);
    $this->assertAttributeEquals([$customExtractor,$objectExtractor],'extractors',$chainExtractor,'extractors');

    return $chainExtractor;
  }

  public function testInvalidArgumentsConstructor()
  {
    $this->expectException(\InvalidArgumentException::class);

    $customExtractor = new CustomExtractor();
    $stdClass = new \stdClass();
    $chainExtractor = new ChainExtractor([$customExtractor,$stdClass]);
  }

  /**
  * @depends testConstructor
  */
  public function testExtractCustom(ChainExtractor $chainExtractor)
  {
    $object = new PersonWithBirthDate();
    $object->firstName = 'John';
    $object->lastName = 'Doe';
    $object->gender = 'male';
    $object->birthDate = new \DateTime('1970-01-01');
    $array = $chainExtractor->extract($object);

    $this->assertEquals('John',$array['firstName'],'firstName');
    $this->assertEquals('Doe',$array['lastName'],'lastName');
    $this->assertEquals('male',$array['gender'],'gender');
    $this->assertEquals('1970-01-01',$array['birthDate'],'birthDate');
  }

  /**
  * @depends testConstructor
  */
  public function testExtractObject(ChainExtractor $chainExtractor)
  {
    $object = new Person();
    $object->firstName = 'John';
    $object->lastName = 'Doe';
    $object->gender = 'male';
    $array = $chainExtractor->extract($object);

    $this->assertEquals('John',$array['firstName'],'firstName');
    $this->assertEquals('Doe',$array['lastName'],'lastName');
    $this->assertEquals('male',$array['gender'],'gender');
  }
}
