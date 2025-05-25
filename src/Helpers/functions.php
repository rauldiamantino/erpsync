<?php function pr($data, $type = false) { ?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Test</title>
  </head>
  <body class="bg-black text-gray-200">
    <pre>

    <?php if ($type) {
      var_dump(print_r($data));
    }
    else {
      print_r($data);
    }
    ?>
  </body>
  </html>
  </pre>
<?php } ?>
