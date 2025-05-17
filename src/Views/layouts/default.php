<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Meu Site'; ?></title>
  </head>
  <body>
    <header>
      <h1>Meu Site Incrível</h1>
    </header>
    <main>
      <?php echo $content; ?>
    </main>
    <footer>
      <p>&copy; <?php echo date('Y'); ?> Todos os direitos reservados.</p>
    </footer>
  </body>
</html>