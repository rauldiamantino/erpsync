<nav class="w-full flex justify-start gap-10 bg-white shadow-md p-4">
  <div class="max-w-7xl flex items-start gap-8">
    <div class="text-xl font-bold text-gray-800">Bling</div>
    <ul class="space-y-2">
      <?php foreach ($blingUrls as $value): ?>
        <li class="text-gray-700 hover:text-blue-600 cursor-pointer">
          <a href="<?php echo $value['url']; ?>"><?php echo $value['description']; ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</nav>

<div class="overflow-x-auto">
  <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">ID</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Tipo</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Referência</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Serviço</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Tentativas</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Criado</th>
        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Atualizado</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white">
      <?php foreach ($schedules as $value): ?>
        <tr>
          <td class="px-4 py-2 text-sm text-gray-700"><?php echo $value['id'] ?? ''; ?></td>
          <td class="px-4 py-2 text-sm text-gray-700"><?php echo $value['type'] ?? ''; ?></td>
          <td class="px-4 py-2 text-sm text-gray-700"><?php echo $value['reference_id'] ?? ''; ?></td>
          <td class="px-4 py-2 text-sm text-gray-700"><?php echo $value['service'] ?? ''; ?></td>
          <td class="px-4 py-2 text-sm text-gray-700"><?php echo $value['status'] ?? ''; ?></td>
          <td class="px-4 py-2 text-sm text-gray-700"><?php echo $value['attempts'] ?? ''; ?></td>
          <td class="px-4 py-2 text-sm text-gray-700"><?php echo $value['created_at'] ?? ''; ?></td>
          <td class="px-4 py-2 text-sm text-gray-700"><?php echo $value['updated_at'] ?? ''; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>