<div class="min-w-full">
  <section class="w-full flex justify-end px-2 py-3">
    <a href="/integration_tasks" class="block w-max text-center text-gray-700 hover:underline" role="button" aria-label="Ver envios feitos">Ver agendamentos</a>
  </section>

  <div class="overflow-auto border border-gray-300 rounded-lg shadow-sm bg-white">
    <table class="min-w-[1200px] w-full table-auto divide-y divide-gray-200">
      <thead class="bg-gray-100 border-b border-gray-300">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-16">ID</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-24">Tipo</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-32">Serviço (De)</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-32">Serviço (Para)</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-40">Referência</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 min-w-[400px]">Requisição</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 min-w-[400px]">Resposta</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-40">Criado</th>
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-40">Atualizado</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 text-gray-700 text-sm">
        <?php foreach ($integrationLogs as $value): ?>
          <tr class="hover:bg-gray-50 transition">
            <td class="px-4 py-2 truncate w-16"><?php echo $value['id'] ?? ''; ?></td>
            <td class="px-4 py-2 truncate w-24"><?php echo $value['type'] ?? ''; ?></td>
            <td class="px-4 py-2 truncate w-32"><?php echo $value['service_from'] ?? ''; ?></td>
            <td class="px-4 py-2 truncate w-32"><?php echo $value['service_to'] ?? ''; ?></td>
            <td class="px-4 py-2 truncate w-40"><?php echo $value['reference_id'] ?? ''; ?></td>
            <td class="px-4 py-2 align-top min-w-[400px]">
              <div class="max-h-[150px] overflow-auto p-3 bg-gray-100 border border-gray-200 rounded text-xs font-mono whitespace-pre-wrap break-words">
                <pre class="whitespace-pre-wrap break-all"><?php echo htmlspecialchars($value['request_body'] ?? ''); ?></pre>
              </div>
            </td>
            <td class="px-4 py-2 truncate align-top min-w-[400px]">
              <div class="max-h-[150px] overflow-auto p-3 bg-gray-100 border border-gray-200 rounded text-xs font-mono whitespace-pre-wrap break-words">
                <pre class="whitespace-pre-wrap break-all"><?php echo htmlspecialchars($value['response_body'] ?? ''); ?></pre>
              </div>
            </td>
            <td class="px-4 py-2 truncate w-40 truncate"><?php echo $value['created_at'] ?? ''; ?></td>
            <td class="px-4 py-2 truncate w-40 truncate"><?php echo $value['updated_at'] ?? ''; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>