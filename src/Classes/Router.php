<?php

namespace App\Classes;

class Router
{
  protected string $url;
  protected string $controllerNamespace = 'App\\Controllers\\';
  protected string $defaultController = 'HomeController';
  protected string $defaultMethod = 'index';
  protected array $routes = [];

  public function __construct(string $url)
  {
    $this->url = trim(parse_url($url, PHP_URL_PATH), '/');
  }

  public function addRoute(string $path, string $controller, string $method): void
  {
    $this->routes[ $path ] = [
      'controller' => ucfirst($controller) . 'Controller',
      'method' => $method,
    ];
  }

  public function dispatch(): void
  {
    $segments = explode('/', $this->url);

    $controllerName = ucfirst($segments[0] ?? '') . 'Controller';

    if (empty($controllerName) or $controllerName === 'Controller') {
      $controllerName = $this->defaultController;
    }

    $method = $segments[1] ?? $this->defaultMethod;
    $params = array_slice($segments, 2);

    $controllerClass = $this->controllerNamespace . $controllerName;

    if (! class_exists($controllerClass)) {
      http_response_code(404);
      echo "Controller not found.";
      exit;
    }

    $controller = new $controllerClass();

    if (! method_exists($controller, $method)) {
      http_response_code(404);
      echo "Method not found.";
      exit;
    }

    call_user_func_array([ $controller, $method ], $params);
  }
}
