<?php

namespace App\Classes;

class View
{
  protected string $basePath;
  protected array $data = [];
  protected ?string $layoutPath = null;

  public function __construct()
  {
    $this->basePath = __DIR__ . '/../Views';
  }

  public function assign(string $key, $value): void
  {
    $this->data[$key] = $value;
  }

  public function setLayout(string $layout): void
  {
    $this->layoutPath = $this->basePath . '/layouts/' . $layout . '.php';
  }

  public function setFolder(string $folder): void
  {
    if ($folder) {
      $this->basePath .= '/' . $folder;
    }
  }

  public function render(string $view, array $data = []): void
  {
    $viewPath = $this->basePath . '/' . str_replace('.', '/', $view) . '.php';

    if (! file_exists($viewPath)) {
      throw new \InvalidArgumentException("View not found: {$view}");
    }

    // Extract data to local variables
    extract(array_merge($this->data, $data));

    // Start output buffering
    ob_start();

    // Include the view file
    include $viewPath;

    // Get the view content
    $content = ob_get_clean();

    // Include the layout, if defined
    if ($this->layoutPath !== null and file_exists($this->layoutPath)) {
      include $this->layoutPath;
    }
    else {
      echo $content;
    }
  }
}
