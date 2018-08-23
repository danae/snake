<?php
namespace Snake\Common;

trait NameCallbackTrait
{
  // Variables
  protected $nameCallbacks = [];

  // Set the name callbacks
  public function setNameCallbacks(array $nameCallbacks): self
  {
    foreach ($nameCallbacks as $name => $callback)
      if (!is_string($name) || !is_callable($callback))
        throw new \InvalidArgumentException("Name callbacks must be an associative-only array containing callables");

    $this->nameCallbacks = $nameCallbacks;
    return $this;
  }

  // Apply the name callbacks
  private function applyNameCallbacks($name, $value)
  {
    if (array_key_exists($name,$this->nameCallbacks))
      $value = $this->nameCallbacks[$name]($value);
    return $value;
  }
}
