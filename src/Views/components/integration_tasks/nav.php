<nav class="w-full sm:w-[220px] flex flex-col gap-6 p-4 bg-white rounded-xl shadow-lg">
  <section class="w-full">
    <h2 class="text-xl font-semibold text-gray-800 mb-3">Receive <span class="text-sm font-light italic text-gray-500">(ERP)</span></h2>
    <div class="flex flex-col gap-2">
      <?php foreach ($receiveUrls as $value): ?>
        <a href="<?php echo $value['url']; ?>"
          class="flex items-center gap-3 px-3 py-2 text-gray-700 rounded-md hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
          role="button">
          <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
          </svg>
          <span class="text-base"><?php echo $value['description']; ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="w-full">
    <h2 class="text-xl font-semibold text-gray-800 mb-3">Send <span class="text-sm font-light italic text-gray-500">(E-commerce)</span></h2>
    <div class="flex flex-col gap-2">
      <?php foreach ($sendUrls as $value): ?>
        <a href="<?php echo $value['url']; ?>"
          class="flex items-center gap-3 px-3 py-2 text-gray-700 rounded-md hover:bg-green-100 hover:text-green-700 transition-colors duration-200"
          role="button">
          <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v4a3 3 0 003 3h4m-4 6h0m-7-9l4-4m0 0l4 4m-4-4v12"></path>
          </svg>
          <span class="text-base"><?php echo $value['description']; ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
</nav>