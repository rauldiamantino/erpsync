<?php

class Container
{
  protected array $bindings = [];

  // Registra uma classe e sua factory para criar
  public function bind(string $abstract, callable $factory)
  {
    $this->bindings[ $abstract ] = $factory;
  }

  // Cria a instância da classe
  public function make(string $abstract)
  {
    if (isset($this->bindings[ $abstract ])) {
      return $this->bindings[ $abstract ]($this);
    }

    // Tenta criar a classe diretamente (sem parâmetros no construtor)
    return new $abstract();
  }
}
