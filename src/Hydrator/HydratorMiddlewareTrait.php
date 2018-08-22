<?php
namespace Snake\Hydrator;

trait HydratorMiddlewareTrait
{
  // Variables
  private $before = [];
  private $after = [];

  // Set the before middleware
  public function setBefore(array $before): self
  {
    foreach ($before as $callback)
      if (!is_callable($callback))
        throw new \InvalidArgumentException("Middleware must be an indexed array of callables");

    $this->before = $before;
    return $this;
  }

  // Set the after middlafeware
  public function setAfter(array $after): self
  {
    foreach ($after as $callback)
      if (!is_callable($callback))
        throw new \InvalidArgumentException("Middleware must be an indexed array of callables");

    $this->after = $after;
    return $this;
  }

  // Apply the before middleware
  protected function applyBefore(array $array): array
  {
    foreach ($this->before as $middleware)
      $array = $middleware($array);
    return $array;
  }

  // Apply the after middleware
  protected function applyAfter(object $object): object
  {
    foreach ($this->after as $middleware)
      $object = $middleware($object);
    return $object;
  }
}
