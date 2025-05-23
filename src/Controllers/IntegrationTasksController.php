<?php

namespace App\Controllers;

use App\Classes\Flash;
use App\Helpers\TypeHelper;
use App\Controllers\Controller;
use App\Helpers\RedirectHelper;
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

  public function index(): void
  {
    $receiveUrls = $this->getActionUrl(TasksAction::RECEIVE);
    $sendUrls = $this->getActionUrl(TasksAction::SEND);

    $integrationTasks = $this->integrationTaskModel->all();

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

  private function getActionUrl(int $taskAction): array
  {
    $taskActionUrls = [
      TasksAction::RECEIVE => [
        ['url' => '/integration_tasks/receive_category/' . ServiceType::BLING, 'description' => 'Categorias'],
        ['url' => '/integration_tasks/receive_supplier/' . ServiceType::BLING, 'description' => 'Fornecedores'],
        ['url' => '/integration_tasks/receive_product/' . ServiceType::BLING, 'description' => 'Produtos'],
      ],
      TasksAction::SEND => [
        ['url' => '/integration_tasks/send_category/' . ServiceType::BLING, 'description' => 'Categorias'],
        ['url' => '/integration_tasks/send_supplier/' . ServiceType::BLING, 'description' => 'Fornecedores'],
        ['url' => '/integration_tasks/send_product/' . ServiceType::BLING, 'description' => 'Produtos'],
      ],
    ];

    return $taskActionUrls[ $taskAction ] ?? [];
  }

  public function receiveCategory(int $serviceType): void
  {
    if ($serviceType === ServiceType::BLING) {
      $result = (new BlingCategorySchedulerComponent($this->integrationTaskModel))->scheduleSync();

      if (isset($result['error']) or ! isset($result['total_scheduled'])) {
        RedirectHelper::to('/integration_tasks', 'error', 'Unable to receive categories');
      }

      RedirectHelper::to('/integration_tasks', 'success', $result['total_scheduled'] . ' categories reiceved');
    }
  }

  public function receiveSupplier(int $serviceType): void
  {
    if ($serviceType === ServiceType::BLING) {
      $result = (new BlingSupplierSchedulerComponent($this->integrationTaskModel))->scheduleSync();

      if (isset($result['error']) or ! isset($result['total_scheduled'])) {
        RedirectHelper::to('/integration_tasks', 'error', 'Unable to receive suppliers');
      }

      RedirectHelper::to('/integration_tasks', 'success', $result['total_scheduled'] . ' suppliers reiceved');
    }
  }

  public function receiveProduct(int $serviceType): void
  {
    if ($serviceType === ServiceType::BLING) {
      $result = (new BlingProductSchedulerComponent($this->integrationTaskModel))->scheduleSync();

      if (isset($result['error']) or ! isset($result['total_scheduled'])) {
        RedirectHelper::to('/integration_tasks', 'error', 'Unable to receive products');
      }

      if (isset($result['neutral'])) {
        RedirectHelper::to('/integration_tasks', 'neutral', $result['neutral']);
      }

      RedirectHelper::to('/integration_tasks', 'success', $result['total_scheduled'] . ' products received');
    }
  }

  public function sendCategory(int $serviceType): void
  {
    if ($serviceType === ServiceType::BLING) {
      $result = (new SyncController())->sync(ReferenceType::CATEGORY);

      if (isset($result['error'])) {
        RedirectHelper::to('/integration_tasks', 'error', $result['error']);
      }

      if (isset($result['neutral'])) {
        RedirectHelper::to('/integration_tasks', 'neutral', $result['neutral']);
      }

      RedirectHelper::to('/integration_tasks', 'success', $result['total_synchronized'] . ' categories submitted');
    }
  }

  public function sendSupplier(int $serviceType): void
  {
    if ($serviceType === ServiceType::BLING) {
      $result = (new SyncController())->sync(ReferenceType::SUPPLIER);

      if (isset($result['error'])) {
        RedirectHelper::to('/integration_tasks', 'error', $result['error']);
      }

      if (isset($result['neutral'])) {
        RedirectHelper::to('/integration_tasks', 'neutral', $result['neutral']);
      }

      RedirectHelper::to('/integration_tasks', 'success', $result['total_synchronized'] . ' categories submitted');
    }
  }

  public function sendProduct(int $serviceType): void
  {
    if ($serviceType === ServiceType::BLING) {
      RedirectHelper::to('/integration_tasks', 'success', 0 . ' products submitted');
    }
  }
}