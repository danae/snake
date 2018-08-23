<?php
namespace Snake\Common;

trait NameConvertorTrait
{
  // Variables
  protected $nameConvertors = [];

  // Set the name convertors
  public function setNameConvertors(array $nameConvertors): self
  {
    foreach ($nameConvertors as $name => $mappedName)
      if (!is_string($name) || !is_string($mappedName))
        throw new \InvalidArgumentException("Name converters must be an associative-only array containing strings");

    $this->nameConvertors = $nameConvertors;
    return $this;
  }

  // Apply the name convertors
  private function applyNameConvertors($name)
  {
    if (array_key_exists($name,$this->nameConvertors))
      $name = $this->nameConvertors[$name];
    return $name;
  }
}
