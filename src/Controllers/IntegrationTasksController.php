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
use App\Classes\Constants\TasksAction;
use App\Classes\Constants\ReferenceType;

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

    $perPage = 10;
    $currentPage = intval($_GET['page'] ?? 1);

    $columns = [
      'id',
      'type',
      'reference_id',
      'service',
      'attempts',
      'created_at',
      'updated_at',
    ];

    $integrationTasks = $this->integrationTaskModel->allPagination($currentPage, $perPage, $columns);
    $integrationTasksTotal = $this->integrationTaskModel->count();

    $currentPage = max((int)$currentPage, 1);
    $this->view->assign('currentPage', $currentPage);

    $totalPages = (int) ceil($integrationTasksTotal / $perPage);
    $this->view->assign('totalPages', $totalPages);

    foreach ($integrationTasks as $key => $value):
      $integrationTasks[ $key ]['type'] = TypeHelper::getReferenceName($value['type']);
      $integrationTasks[ $key ]['service'] = TypeHelper::getServiceName($value['service']);
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

  public function show(int $id)
  {
    $columns = [
      'id',
      'request_body',
      'response_body',
    ];

    $integrationTask = $this->integrationTaskModel->find($id, $columns);

    $id = $integrationTask['id'] ?? '';
    $requestBody = $integrationTask['request_body'] ?? '';
    $responseBody = $integrationTask['response_body'] ?? '';

    $this->view->assign('title', 'Integration Task');
    $this->view->assign('successMessage', Flash::get('success'));
    $this->view->assign('errorMessage', Flash::get('error'));
    $this->view->assign('neutralMessage', Flash::get('neutral'));
    $this->view->assign('id', $id);
    $this->view->assign('requestBody', ConversionHelper::formatterJson($requestBody));
    $this->view->assign('responseBody', ConversionHelper::formatterJson($responseBody));
    $this->view->render('task');
  }

  private function getActionUrl(int $taskAction): array
  {
    $taskActionUrls = [
      TasksAction::RECEIVE => [
        ['url' => '/integration_tasks/receive/' . ReferenceType::CATEGORY, 'description' => 'Categories'],
        ['url' => '/integration_tasks/receive/' . ReferenceType::SUPPLIER, 'description' => 'Suppliers'],
        ['url' => '/integration_tasks/receive/' . ReferenceType::PRODUCT, 'description' => 'Products'],
      ],
      TasksAction::SEND => [
        ['url' => '/integration_tasks/send/' . ReferenceType::CATEGORY, 'description' => 'Category'],
        ['url' => '/integration_tasks/send/' . ReferenceType::SUPPLIER, 'description' => 'Supplier'],
        ['url' => '/integration_tasks/send/' . ReferenceType::PRODUCT, 'description' => 'Product'],
        ['url' => '/integration_tasks/send/' . ReferenceType::SKU, 'description' => 'Sku'],
        ['url' => '/integration_tasks/send/' . ReferenceType::STOCK, 'description' => 'Stock'],
        ['url' => '/integration_tasks/send', 'description' => 'Next'],
      ],
    ];

    return $taskActionUrls[ $taskAction ] ?? [];
  }

  public function receive(int $referenceType): void
  {
    $syncController = new SyncController();
    $result = $syncController->syncReceive($referenceType);

    $type = $result['type'] ?? 'error';
    $message = $result['message'] ?? '';

    RedirectHelper::to('/integration_tasks', $type, $message);
  }

  public function send(int $referenceType = 0): void
  {
    $syncController = new SyncController();
    $result = $syncController->syncSend($referenceType);

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