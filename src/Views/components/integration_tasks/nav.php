<nav class="w-full sm:w-[200px] flex flex-col gap-4">
  <section class="w-full">
    <h2 class="text-2xl font-extrabold text-gray-900 mb-4">Receber <span class="text-sm font-light italic text-gray-500">(ERP)</span></h2>
    <div class="flex flex-col gap-3">
      <?php foreach ($receiveUrls as $value): ?>
        <a href="<?php echo $value['url']; ?>"
          class="inline-block px-4 py-2 border border-blue-600 text-blue-600 rounded-md hover:bg-blue-600 hover:text-white transition-colors text-center"
          role="button">
          <?php echo $value['description']; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="w-full">
    <h2 class="text-2xl font-extrabold text-gray-900 mb-4">Enviar <span class="text-sm font-light italic text-gray-500">(Ecommerce)</span></h2>
    <div class="flex flex-col gap-3">
      <?php foreach ($sendUrls as $value): ?>
        <a href="<?php echo $value['url']; ?>"
          class="inline-block px-4 py-2 border border-green-600 text-green-600 rounded-md hover:bg-green-600 hover:text-white transition-colors text-center"
          role="button">
          <?php echo $value['description']; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
</nav>