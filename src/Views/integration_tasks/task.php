<div class="flex items-center justify-center p-4 sm:p-6">
  <div class="w-full max-w-4xl bg-white shadow-lg rounded-xl p-6 sm:p-8">
    <section class="mb-6">
      <h2 class="text-3xl font-bold text-gray-800 text-center mb-2">Request/Response Details #<?php echo $id; ?></h2>
      <p class="text-center text-gray-600">View the request and response body in JSON format.</p>
    </section>

    <div class="flex justify-center mt-8">
      <a href="<?php echo $_GET['referer'] ?? '/integration_tasks'; ?>" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
        Back to tasks
      </a>
    </div>

    <div class="space-y-6">
      <div>
        <label for="requestBody" class="block text-lg font-semibold text-gray-700 mb-2">Request Body:</label>
        <div class="h-80 overflow-auto p-4 bg-gray-100 border border-gray-200 rounded-lg text-sm font-mono text-gray-800 whitespace-pre-wrap break-words shadow-inner">
          <pre id="requestBody" class="whitespace-pre-wrap break-all"><?php echo $requestBody; ?></pre>
        </div>
      </div>

      <div>
        <label for="responseBody" class="block text-lg font-semibold text-gray-700 mb-2">Response Body:</label>
        <div class="h-80 overflow-auto p-4 bg-gray-100 border border-gray-200 rounded-lg text-sm font-mono text-gray-800 whitespace-pre-wrap break-words shadow-inner">
          <pre id="responseBody" class="whitespace-pre-wrap break-all"><?php echo $responseBody; ?></pre>
        </div>
      </div>
    </div>
  </div>
</div>