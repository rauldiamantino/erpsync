<?php use App\Helpers\ElementHelper; ?>

<div class="w-full flex flex-col sm:flex-row sm:justify-center gap-4 min-h-screen">
  <?php ElementHelper::render('/integration_tasks/nav.php', ['receiveUrls' => $receiveUrls, 'sendUrls' => $sendUrls]); ?>

  <div class="w-full overflow-x-auto">
    <section class="w-full flex justify-end px-2 py-3">
      <a href="/integration_logs" class="block w-max text-center text-gray-700 hover:underline" role="button" aria-label="Ver envios feitos">Ver envios realizados</a>
    </section>

    <div class="w-full overflow-x-auto border border-gray-300 rounded-lg shadow-sm bg-white">
      <table class="min-w-[1200px] w-full table-auto divide-y divide-gray-200">
        <thead class="bg-gray-100 border-b border-gray-300">
          <tr>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-16">ID</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-24">Tipo</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-32">Serviço</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-40">Referência</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-40">Status</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-40">Tentativas</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 min-w-[400px]">Requisição</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 min-w-[400px]">Resposta</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-40">Criado</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-40">Atualizado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-gray-700 text-sm">
          <?php foreach ($integrationTasks as $value): ?>
            <tr class="hover:bg-gray-50 transition">
              <td class="px-4 py-2 truncate w-16"><?php echo $value['id'] ?? ''; ?></td>
              <td class="px-4 py-2 truncate w-24"><?php echo $value['type'] ?? ''; ?></td>
              <td class="px-4 py-2 truncate w-32"><?php echo $value['service'] ?? ''; ?></td>
              <td class="px-4 py-2 truncate w-40"><?php echo $value['reference_id'] ?? ''; ?></td>
              <td class="px-4 py-2 truncate w-40"><?php echo $value['status'] ?? ''; ?></td>
              <td class="px-4 py-2 truncate w-40"><?php echo $value['attempts'] ?? ''; ?></td>
              <td class="px-4 py-2 align-top min-w-[400px]">
                <div class="max-h-[150px] overflow-auto p-3 bg-gray-100 border border-gray-200 rounded text-xs font-mono whitespace-pre-wrap break-words">
                  <?php echo htmlspecialchars($value['request_body'] ?? ''); ?>
                </div>
              </td>
              <td class="px-4 py-2 align-top min-w-[400px]">
                <div class="max-h-[150px] overflow-auto p-3 bg-gray-100 border border-gray-200 rounded text-xs font-mono whitespace-pre-wrap break-words">
                  <?php echo htmlspecialchars($value['response_body'] ?? ''); ?>
                </div>
              </td>
              <td class="px-4 py-2 truncate w-40"><?php echo $value['created_at'] ?? ''; ?></td>
              <td class="px-4 py-2 truncate w-40"><?php echo $value['updated_at'] ?? ''; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>