<div class="mb-16 min-w-full p-4 sm:p-6 bg-white rounded-xl shadow-lg">
  <section class="w-full flex justify-between items-center px-2 py-3 mb-4">
    <div>
      <h2 class="text-2xl font-bold text-gray-800">Integration Logs</h2>
      <span class="text-md text-gray-600">Total Records: <?php echo $totalLogs; ?></span>
    </div>
    <a href="/integration_tasks" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" role="button" aria-label="Ver agendamentos">
      View Schedules
    </a>
  </section>

  <div class="overflow-x-auto">
    <table class="min-w-[1400px] w-full table-auto divide-y divide-gray-300">
      <thead class="bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">ID</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Type</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Service (From)</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Service (To)</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-48">Reference</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-48">Created at</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-48">Updated at</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200 text-gray-700 text-sm">
        <?php foreach ($integrationLogs as $value): ?>
          <tr class="hover:bg-gray-100 transition duration-150 ease-in-out">
            <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
              <a href="/integration_logs/show/<?php echo $value['id'] . '?referer=' . urlencode($_SERVER['REQUEST_URI']); ?>" class="underline">
                <?php echo $value['id']; ?>
              </a>
            </td>
            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo $value['type']; ?></td>
            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo $value['service_from']; ?></td>
            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo $value['service_to']; ?></td>
            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo $value['reference_id']; ?></td>
            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo $value['created_at']; ?></td>
            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo $value['updated_at']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>