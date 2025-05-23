<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title><?php echo $title ?? 'Meu Site'; ?></title>
  </head>
  <body class="h-full min-h-screen flex flex-col">
    <header class="p-4 border-b border-gray-300">
      <h1 class="text-xl"><a href="/">ErpSync</a></h1>
    </header>

    <main class="flex-1 w-full p-4 overflow-auto">
      <!-- Mensagens -->
      <?php if (isset($successMessage)) { ?>
        <div class="w-full bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
          <strong class="font-bold">Success!</strong>
          <span class="block sm:inline"><?php echo $successMessage; ?></span>
        </div>
      <?php } ?>

      <?php if (isset($errorMessage)) { ?>
        <div class="w-full bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
          <strong class="font-bold">Error!</strong>
          <span class="block sm:inline"><?php echo $errorMessage; ?></span>
        </div>
      <?php } ?>

      <?php if (isset($neutralMessage)) { ?>
        <div class="w-full bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded" role="alert">
          <span class="block sm:inline"><?php echo $neutralMessage; ?></span>
        </div>
      <?php } ?>

      <?php echo $content; ?>
    </main>

    <footer class="w-full flex items-center justify-center p-4 bg-gray-100">
      <p>&copy; <?php echo date('Y'); ?> Todos os direitos reservados.</p>
    </footer>
  </body>
</html>