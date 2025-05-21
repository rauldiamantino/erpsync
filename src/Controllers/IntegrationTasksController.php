<?php

namespace App\Controllers;

use App\Helpers\Flash;
use App\Models\IntegrationTaskModel;
use App\Controllers\Controller;
use App\Classes\Constants\ServiceType;
use App\Classes\Constants\TasksAction;
use App\Controllers\Components\BlingCategorySchedulerComponent;
use App\Controllers\Components\BlingProductSchedulerComponent;
use App\Controllers\SyncController;

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
      $integrationTasks[ $key ]['type'] = $this->getReferenceName($value['type']);
      $integrationTasks[ $key ]['service'] = $this->getServiceName($value['service']);
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
        ['url' => '/integration_tasks/categories/' . ServiceType::BLING . '/' . TasksAction::RECEIVE, 'description' => 'Categorias'],
        ['url' => '/integration_tasks/products/' . ServiceType::BLING . '/' . TasksAction::RECEIVE, 'description' => 'Produtos'],
      ],
      TasksAction::SEND => [
        ['url' => '/integration_tasks/categories/' . ServiceType::BLING . '/' . TasksAction::SEND, 'description' => 'Categorias'],
        ['url' => '/integration_tasks/products/' . ServiceType::BLING . '/' . TasksAction::SEND, 'description' => 'Produtos'],
      ],
    ];

    return $taskActionUrls[ $taskAction ] ?? [];
  }

  public function categories(int $serviceType, int $taskStatus): void
  {
    if ($serviceType === ServiceType::BLING and $taskStatus === TasksAction::RECEIVE) {
      $blingScheduleSync = new BlingCategorySchedulerComponent($this->integrationTaskModel);

      $result = $blingScheduleSync->scheduleSync();

      if (isset($result['error']) or ! isset($result['total_scheduled'])) {
        $this->redirect('/integration_tasks', 'error', 'Unable to receive categories');
      }

      $this->redirect('/integration_tasks', 'success', $result['total_scheduled'] . ' categories reiceved');
    }

    if ($serviceType === ServiceType::BLING and $taskStatus === TasksAction::SEND) {
      $syncController = new SyncController();

      $result = $syncController->sync();

      if (isset($result['error'])) {
        $this->redirect('/integration_tasks', 'error', $result['error']);
      }

      if (isset($result['neutral'])) {
        $this->redirect('/integration_tasks', 'neutral', $result['neutral']);
      }

      $this->redirect('/integration_tasks', 'success', $result['total_synchronized'] . ' categories submitted');
    }
  }

  public function products(int $serviceType, int $taskStatus): void
  {
    if ($serviceType === ServiceType::BLING and $taskStatus === TasksAction::RECEIVE) {
      $blingScheduleSync = new BlingProductSchedulerComponent($this->integrationTaskModel);

      $result = $blingScheduleSync->scheduleSync();

      if (isset($result['error']) or ! isset($result['total_scheduled'])) {
        $this->redirect('/integration_tasks', 'error', 'Unable to receive products');
      }

      if (isset($result['neutral'])) {
        $this->redirect('/integration_tasks', 'neutral', $result['neutral']);
      }

      $this->redirect('/integration_tasks', 'success', $result['total_scheduled'] . ' products received');
    }

    if ($serviceType === ServiceType::BLING and $taskStatus === TasksAction::SEND) {
      $this->redirect('/integration_tasks', 'success',  $result['total_synchronized'] . ' products submitted');
    }
  }
}