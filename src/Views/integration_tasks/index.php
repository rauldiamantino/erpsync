<div class="w-full">
  <div class="w-full flex gap-2 justify-end mb-4">
    <button
      id="btn-receive"
      type="button"
      <?php // onclick="window.location.href='/integration_tasks'" ?>
      class="cursor-pointer rounded-md bg-green-600 hover:bg-green-700 px-3 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-green-700"
    >
      Receber
    </button>

    <button
      id="btn-send"
      type="button"
      <?php // onclick="window.location.href='/integration_tasks'" ?>
      class="cursor-pointer rounded-md bg-blue-600 hover:bg-blue-700 px-3 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-blue-700"
    >
      Enviar
    </button>
  </div>

  <!-- <nav class="w-full flex justify-start gap-10 p-4">
    <div class="max-w-7xl flex items-start gap-8">
      <div class="text-xl font-bold text-gray-800">Receber <span class="text-base font-light italic">(ERP)</span></div>
      <ul class="space-y-2">
        <?php foreach ($receiveUrls as $value): ?>
          <li class="text-gray-700 hover:text-blue-600 cursor-pointer">
            <a href="<?php echo $value['url']; ?>"><?php echo $value['description']; ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="max-w-7xl flex items-start gap-8">
      <div class="text-xl font-bold text-gray-800">Enviar <span class="text-base font-light italic">(Ecommerce)</span></div>
      <ul class="space-y-2">
        <?php foreach ($sendUrls as $value): ?>
          <li class="text-gray-700 hover:text-blue-600 cursor-pointer">
            <a href="<?php echo $value['url']; ?>"><?php echo $value['description']; ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </nav> -->

  <div class="w-full overflow-auto">
    <table class="min-w-[1200px] border border-gray-300 divide-y divide-gray-200 table-auto">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-16">ID</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-24">Tipo</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-32">Serviço</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-40">Referência</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-40">Status</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-40">Tentativas</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-[400px]">Requisição</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-[400px]">Resposta</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-40">Criado</th>
          <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 w-40">Atualizado</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 bg-white">
        <?php foreach ($integrationTasks as $value): ?>
          <tr>
            <td class="px-4 py-2 text-sm text-gray-700 w-16"><?php echo $value['id'] ?? ''; ?></td>
            <td class="px-4 py-2 text-sm text-gray-700 w-24"><?php echo $value['type'] ?? ''; ?></td>
            <td class="px-4 py-2 text-sm text-gray-700 w-32"><?php echo $value['service'] ?? ''; ?></td>
            <td class="px-4 py-2 text-sm text-gray-700 w-40"><?php echo $value['reference_id'] ?? ''; ?></td>
            <td class="px-4 py-2 text-sm text-gray-700 w-40"><?php echo $value['status'] ?? ''; ?></td>
            <td class="px-4 py-2 text-sm text-gray-700 w-40"><?php echo $value['attempts'] ?? ''; ?></td>
            <td class="px-4 py-2 text-sm text-gray-700 align-top w-[500px]">
              <div class="h-[150px] min-w-[400px] overflow-y-auto overflow-x-auto p-2 bg-gray-200 border border-gray-200 rounded text-xs">
                <pre class="whitespace-pre-wrap break-all"><?php echo htmlspecialchars($value['request_body'] ?? ''); ?></pre>
              </div>
            </td>
            <td class="px-4 py-2 text-sm text-gray-700 align-top w-[500px]">
              <div class="h-[150px] min-w-[400px] overflow-y-auto overflow-x-auto p-2 bg-gray-200 border border-gray-200 rounded text-xs">
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