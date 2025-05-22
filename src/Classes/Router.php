<?php

namespace App\Classes;

class Router
{
  protected string $url;
  protected string $controllerNamespace = 'App\\Controllers\\';
  protected string $defaultController = 'HomeController';
  protected string $defaultMethod = 'index';
  protected array $routes = [];

  public function __construct(?string $url = null)
  {
    $url = $url ?? $_SERVER['REQUEST_URI'];
    $this->url = trim(parse_url($url, PHP_URL_PATH), '/');
  }

  public function addRoute(string $path, string $controller, string $method): void
  {
    $controller = $this->formatControllerName($controller);

    if ($controller) {
      $this->routes[ $path ] = ['controller' => $controller, 'method' => $method];
    }
  }

  public function dispatch(): void
  {
    $urlData = $this->parseUrl();

    $controllerName = $urlData['controller'];
    $method = $urlData['method'];
    $params = $urlData['params'];
    $routeKey = $urlData['routeKey'];

    if (isset($this->routes[ $routeKey ])) {
      $route = $this->routes[ $routeKey ];
      $controllerName = $route['controller'];
      $method = $route['method'];
    }
    else {
      $controllerName = $this->formatControllerName($controllerName);
    }

    if (empty($controllerName)) {
      $controllerName = $this->defaultController;
    }

    if (empty($method)) {
      $method = $this->defaultMethod;
    }

    $controllerClass = $this->controllerNamespace . $controllerName;

    if (! class_exists($controllerClass)) {
      http_response_code(404);
      echo 'Controller not found.';
      exit;
    }

    $controller = new $controllerClass();

    if (! method_exists($controller, $method)) {
      http_response_code(404);
      echo 'Method not found.';
      exit;
    }

    call_user_func_array([$controller, $method], $params);
  }

  private function parseUrl(): array
  {
    $segments = explode('/', trim($this->url, '/'));

    $controller = $segments[0] ?? '';
    $method = $segments[1] ?? '';
    $params = array_slice($segments, 2);

    $routeSegments = [$controller, $method];
    foreach ($params as $value):
      $routeSegments[] = '{id}';
    endforeach;

    $routeKey = '/' . implode('/', $routeSegments);

    return [
      'controller' => $controller,
      'method' => $method,
      'params' => $params,
      'routeKey' => $routeKey
    ];
  }

  private function formatControllerName(string $nome): string
  {
    if (empty($nome)) {
      return '';
    }

    $camelCase = str_replace(' ', '', ucwords(str_replace('_', ' ', $nome)));

    return $camelCase . 'Controller';
  }
}
