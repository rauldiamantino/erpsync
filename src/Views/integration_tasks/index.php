<?php

use App\Helpers\ElementHelper; ?>

<div class="mb-16 w-full flex flex-col sm:flex-row sm:justify-center gap-6">
  <?php ElementHelper::render('/integration_tasks/nav.php', ['receiveUrls' => $receiveUrls, 'sendUrls' => $sendUrls]); ?>

  <div class="w-full overflow-x-auto bg-white rounded-xl shadow-lg p-4 sm:p-6">
    <section class="w-full flex justify-between items-center px-2 py-3 mb-4">
      <div>
        <h2 class="text-2xl font-bold text-gray-800">Integration Tasks</h2>
        <div class="flex flex-col font-light">
          <span class="text-md text-gray-600">Total records: <?php echo $totalTasks; ?></span>
          <span class="text-md text-gray-600">Total pages: <?php echo $totalPages; ?></span>
          <span class="text-md text-gray-600">Current page: <?php echo $currentPage; ?></span>
        </div>
      </div>
      <a href="/integration_logs" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" role="button" aria-label="Ver envios feitos">
        View Completed Requests
      </a>
    </section>

    <div class="w-full overflow-x-auto">
      <table class="min-w-[1400px] w-full table-auto divide-y divide-gray-300">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">ID</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Type</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Service</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-48">Reference</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Attempts</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-48">Created at</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-48">Updated at</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12"></th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 text-gray-700 text-sm">
          <?php foreach ($integrationTasks as $value): ?>
            <tr class="hover:bg-gray-100 transition duration-150 ease-in-out">
              <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                <a href="/integration_tasks/show/<?php echo $value['id'] . '?referer=' . urlencode($_SERVER['REQUEST_URI']); ?>" class="underline">
                  <?php echo $value['id']; ?>
                </a>
              </td>
              <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo $value['type']; ?></td>
              <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo $value['service']; ?></td>
              <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo $value['reference_id']; ?></td>
              <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo $value['attempts']; ?></td>
              <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo $value['created_at']; ?></td>
              <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo $value['updated_at']; ?></td>
              <td class="px-6 py-3 whitespace-nowrap text-sm text-red-800">
                <a href="/integration_tasks/delete/<?php echo $value['id']; ?>" class="underline">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                  </svg>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>