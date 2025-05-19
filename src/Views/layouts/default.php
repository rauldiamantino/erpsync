<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title><?php echo $title ?? 'Meu Site'; ?></title>
  </head>
  <body class="min-h-screen flex flex-col">
    <header class="p-4 border-b border-gray-300">
      <h1 class="text-xl">ErpSync</h1>
    </header>
    <main class="relative w-full flex-1 p-4">

      <?php if (isset($successMessage)) { ?>
        <div class="absolute top-4 left-1/2 transform -translate-x-1/2 absolute w-max bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
          <strong class="font-bold">Sucesso!</strong>
          <span class="block sm:inline"><?php echo $successMessage; ?></span>
        </div>
      <?php } ?>

      <?php if (isset($errorMessage)) { ?>
        <div class="absolute top-4 left-1/2 transform -translate-x-1/2 absolute w-max bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
          <strong class="font-bold">Erro!</strong>
          <span class="block sm:inline"><?php echo $errorMessage; ?></span>
        </div>
      <?php } ?>

      <?php echo $content; ?>
    </main>
    <footer class="w-full flex items-center justify-center p-4 bg-gray-100">
      <p>&copy; <?php echo date('Y'); ?> Todos os direitos reservados.</p>
    </footer>
  </body>
</html>