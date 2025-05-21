<div class="min-w-full">
  <div class="w-full overflow-auto">
    <table class="w-full border border-gray-300 divide-y divide-gray-200 table-auto">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-16">ID</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-24">Tipo</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-32">Serviço</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-40">Referência</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-[400px]">Requisição</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-[400px]">Resposta</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-40">Criado</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-40">Atualizado</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 bg-white">
        <?php foreach ($integrationLogs as $value): ?>
          <tr>
            <td class="px-4 py-2 text-sm text-gray-700 w-16"><?php echo $value['id'] ?? ''; ?></td>
            <td class="px-4 py-2 text-sm text-gray-700 w-24"><?php echo $value['type'] ?? ''; ?></td>
            <td class="px-4 py-2 text-sm text-gray-700 w-32"><?php echo $value['service'] ?? ''; ?></td>
            <td class="px-4 py-2 text-sm text-gray-700 w-40"><?php echo $value['reference_id'] ?? ''; ?></td>
            <td class="px-4 py-2 text-sm text-gray-700 align-top w-[500px]">
              <div class="h-[150px] min-w-[400px] overflow-y-auto overflow-x-auto p-2 bg-gray-50 border border-gray-200 rounded text-xs">
                <pre class="whitespace-pre-wrap break-all"><?php echo htmlspecialchars($value['request_body'] ?? ''); ?></pre>
              </div>
            </td>
            <td class="px-4 py-2 text-sm text-gray-700 align-top w-[500px]">
              <div class="h-[150px] min-w-[400px] overflow-y-auto overflow-x-auto p-2 bg-gray-50 border border-gray-200 rounded text-xs">
                <pre class="whitespace-pre-wrap break-all"><?php echo htmlspecialchars($value['response_body'] ?? ''); ?></pre>
              </div>
            </td>
            <td class="px-4 py-2 text-sm text-gray-700 w-40 truncate"><?php echo $value['created_at'] ?? ''; ?></td>
            <td class="px-4 py-2 text-sm text-gray-700 w-40 truncate"><?php echo $value['updated_at'] ?? ''; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>