<?php
namespace Snake\Tests\Extractor;

use PHPUnit\Framework\TestCase;
use Snake\Exception\CannotExtractException;
use Snake\Extractor\DateTimeExtractor;

class DateTimeExtractorTest extends TestCase
{
  public function testConstructor()
  {
    $dateTimeExtractor = new DateTimeExtractor();

    $this->assertInstanceOf(DateTimeExtractor::class,$dateTimeExtractor);

    return $dateTimeExtractor;
  }

  public function testFormatConstructor()
  {
    $dateTimeExtractor = new DateTimeExtractor('Y.m.d');

    $this->assertInstanceOf(DateTimeExtractor::class,$dateTimeExtractor);
    $this->assertAttributeEquals('Y.m.d','format',$dateTimeExtractor,'format');

    return $dateTimeExtractor;
  }

  /**
  * @depends testConstructor
  */
  public function testExtract(DateTimeExtractor $dateTimeExtractor)
  {
    $object = (new \DateTime('1970-01-01T00:00:00+0000'))->setTimeZone(new \DateTimeZone('UTC'));
    $string = $dateTimeExtractor->extract($object);

    $this->assertEquals('1970-01-01T00:00:00+0000',$string,'string');
  }

  /**
  * @depends testConstructor
  */
  public function testCannotExtract(DateTimeExtractor $dateTimeExtractor)
  {
    $this->expectException(CannotExtractException::class);

    $object = new \stdClass();
    $string = $dateTimeExtractor->extract($object);
  }

  /**
  * @depends testFormatConstructor
  */
  public function testFormatExtract(DateTimeExtractor $dateTimeExtractor)
  {
    $object = (new \DateTime('1970-01-01T00:00:00+0000'))->setTimeZone(new \DateTimeZone('UTC'));
    $string = $dateTimeExtractor->extract($object);

    $this->assertEquals('1970.01.01',$string,'string');
  }
}
