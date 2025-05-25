<?php

namespace App\Controllers;

use App\Classes\Flash;
use App\Helpers\TypeHelper;
use App\Classes\CurlRequest;
use App\Controllers\Controller;
use App\Helpers\RedirectHelper;
use App\Helpers\ConversionHelper;
use App\Controllers\SyncController;
use App\Models\IntegrationTaskModel;
use App\Classes\Constants\ServiceType;
use App\Classes\Constants\TasksAction;
use App\Classes\Constants\ReferenceType;
use App\Controllers\Components\BlingProductSchedulerComponent;
use App\Controllers\Components\BlingCategorySchedulerComponent;
use App\Controllers\Components\BlingSupplierSchedulerComponent;

class IntegrationTasksController extends Controller
{
  protected $layout = 'default';
  protected $folder = 'integration_tasks';

  private $integrationTaskModel;

  public function __construct()
  {
    parent::__construct();

    $this->integrationTaskModel = new IntegrationTaskModel();
  }

  public function index()
  {
    $receiveUrls = $this->getActionUrl(TasksAction::RECEIVE);
    $sendUrls = $this->getActionUrl(TasksAction::SEND);

    $integrationTasks = $this->integrationTaskModel->all();

    foreach ($integrationTasks as $key => $value):
      $integrationTasks[ $key ]['type'] = TypeHelper::getReferenceName($value['type']);
      $integrationTasks[ $key ]['service'] = TypeHelper::getServiceName($value['service']);
      $integrationTasks[ $key ]['data'] = ConversionHelper::formatterJson($value['data']);
      $integrationTasks[ $key ]['request_body'] = ConversionHelper::formatterJson($value['request_body']);
      $integrationTasks[ $key ]['response_body'] = ConversionHelper::formatterJson($value['response_body']);
    endforeach;

    $this->view->assign('title', 'Integration Tasks');
    $this->view->assign('successMessage', Flash::get('success'));
    $this->view->assign('errorMessage', Flash::get('error'));
    $this->view->assign('neutralMessage', Flash::get('neutral'));
    $this->view->assign('receiveUrls', $receiveUrls);
    $this->view->assign('sendUrls', $sendUrls);
    $this->view->assign('integrationTasks', $integrationTasks);
    $this->view->render('index');
  }

  private function getActionUrl(int $taskAction): array
  {
    $taskActionUrls = [
      TasksAction::RECEIVE => [
        ['url' => '/integration_tasks/receive_category/' . ServiceType::BLING, 'description' => 'Categorias'],
        ['url' => '/integration_tasks/receive_supplier/' . ServiceType::BLING, 'description' => 'Fornecedores'],
        ['url' => '/integration_tasks/receive_product/' . ServiceType::BLING, 'description' => 'Produtos'],
      ],
      TasksAction::SEND => [
        ['url' => '/integration_tasks/send/' . ReferenceType::CATEGORY, 'description' => 'Categorias'],
        ['url' => '/integration_tasks/send/' . ReferenceType::SUPPLIER, 'description' => 'Fornecedores'],
        ['url' => '/integration_tasks/send/' . ReferenceType::PRODUCT, 'description' => 'Produtos'],
        ['url' => '/integration_tasks/send/' . ReferenceType::SKU, 'description' => 'Skus'],
        ['url' => '/integration_tasks/send/' . ReferenceType::STOCK, 'description' => 'Estoques'],
        ['url' => '/integration_tasks/send', 'description' => 'Todos'],
      ],
    ];

    return $taskActionUrls[ $taskAction ] ?? [];
  }

  public function receiveCategory(int $serviceType)
  {
    if ($serviceType === ServiceType::BLING) {
      $result = (new BlingCategorySchedulerComponent($this->integrationTaskModel))->scheduleSync();

      if (isset($result['error']) or ! isset($result['total_scheduled'])) {
        return RedirectHelper::to('/integration_tasks', 'error', 'Unable to receive categories');
      }

      return RedirectHelper::to('/integration_tasks', 'success', $result['total_scheduled'] . ' categories reiceved');
    }
  }

  public function receiveSupplier(int $serviceType)
  {
    if ($serviceType === ServiceType::BLING) {
      $result = (new BlingSupplierSchedulerComponent($this->integrationTaskModel))->scheduleSync();

      if (isset($result['error']) or ! isset($result['total_scheduled'])) {
        return RedirectHelper::to('/integration_tasks', 'error', 'Unable to receive suppliers');
      }

      return RedirectHelper::to('/integration_tasks', 'success', $result['total_scheduled'] . ' suppliers reiceved');
    }
  }

  public function receiveProduct(int $serviceType)
  {
    if ($serviceType === ServiceType::BLING) {
      $result = (new BlingProductSchedulerComponent($this->integrationTaskModel))->scheduleSync();

      if (isset($result['error']) or ! isset($result['total_scheduled'])) {
        return RedirectHelper::to('/integration_tasks', 'error', 'Unable to receive products');
      }

      if (isset($result['neutral'])) {
        return RedirectHelper::to('/integration_tasks', 'neutral', $result['neutral']);
      }

      return RedirectHelper::to('/integration_tasks', 'success', $result['total_scheduled'] . ' products received');
    }
  }

  public function send(int $referenceType = 0): void
  {
    $result = (new SyncController())->sync($referenceType);

    $type = $result['type'] ?? 'error';
    $taskId = $result['taskId'] ?? '';
    $attempt = intval($result['attempt'] ?? 0);
    $message = $result['message'] ?? '';

    if (CurlRequest::getType() == 'script') {
      $returnScript = [
        'ID: ' => $taskId,
        ucfirst($type) => $message,
        'Attempt: ' => $attempt,
      ];

      echo ConversionHelper::arrayToJson($returnScript);
      exit;
    }

    RedirectHelper::to('/integration_tasks', $type, $message);
  }
}