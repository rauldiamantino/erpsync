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
    <main class="w-full flex-1 p-4">
      <div>
        <?php echo $content; ?>
      </div>
    </main>
    <footer class="w-full flex items-center justify-center p-4 bg-gray-100">
      <p>&copy; <?php echo date('Y'); ?> Todos os direitos reservados.</p>
    </footer>
  </body>
</html>