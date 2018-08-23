<?php
namespace Snake\Common;

trait ContextTrait
{
  // Variables
  protected $context = [];

  // Set the context
  public function setContext(array $context): self
  {
    $this->context = $context;
    return $this;
  }
}
