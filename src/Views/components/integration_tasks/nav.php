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

  <section class="w-full">
    <h2 class="text-xl font-semibold text-gray-800 mb-3">Unschedule<span class="text-sm font-light italic text-gray-500"></span></h2>
    <div class="flex flex-col gap-2">
      <?php foreach ($deleteUrls as $value): ?>
        <a href="<?php echo $value['url']; ?>"
          class="flex items-center gap-3 px-3 py-2 text-gray-700 rounded-md hover:bg-red-100 hover:text-red-800 transition-colors duration-200"
          role="button">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-red-800">
            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
          </svg>
          <span class="text-base"><?php echo $value['description']; ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
</nav>