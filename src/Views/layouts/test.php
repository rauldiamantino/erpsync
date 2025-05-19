<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title><?php echo $title ?? 'Meu Site'; ?></title>
  </head>
  <body class="bg-black min-h-screen flex flex-col">
    <header class="p-4 border-b border-gray-300">
      <h1 class="text-xl text-gray-200">ErpSync's Test</h1>
    </header>
    <main class="w-full p-4">
      <div class="text-gray-200">
        <?php echo $content; ?>
      </div>
    </main>
  </body>
</html>