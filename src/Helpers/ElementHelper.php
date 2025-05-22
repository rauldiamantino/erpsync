<?php

namespace App\Helpers;

class ElementHelper
{
  /**
   * Renders an element (partial view) with passed variables.
   * @param string $path Relative file path of the view (e.g., 'components/button.php')
   * @param array $vars Variables to be extracted inside the view
   * @param bool $returnHtml If true, returns the generated HTML; if false, echoes it directly
   * @return string|null
   * @throws \Exception If the view file does not exist
   */
  public static function render(string $path, array $vars = [], bool $returnHtml = false)
  {
    $file = __DIR__ . '/../Views/components' . $path;

    if (! file_exists($file)) {
      throw new \Exception("Element not found: {$file}");
    }

    // Extract variables to the view's scope
    extract($vars);

    // Start output buffering to capture the view output
    ob_start();
    include $file;
    $content = ob_get_clean();

    if ($returnHtml) {
      return $content;
    }

    echo $content;
    return null;
  }
}