<?php
namespace Snake\Common;

trait TypeCallbackTrait
{
  // Variables
  protected $typeCallbacks = [];

  // Set the type callbacks
  public function setTypeCallbacks(array $typeCallbacks): self
  {
    foreach ($typeCallbacks as $type => $callback)
      if (!is_string($type) || !is_callable($callback))
        throw new \InvalidArgumentException("Type callbacks must be an associative-only array containing callables");

    $this->typeCallbacks = $typeCallbacks;
    return $this;
  }

  // Apply the type callbacks
  private function applyTypeCallbacks($value)
  {
    $type = is_object($value) ? get_class($value) : gettype($value);
    if (array_key_exists($type,$this->typeCallbacks))
      $value = $this->typeCallbacks[$type]($value);
    return $value;
  }
}
