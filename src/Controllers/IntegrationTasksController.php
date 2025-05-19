<?php

namespace App\Controllers;

use App\Helpers\Flash;
use App\Models\IntegrationTaskModel;
use App\Controllers\Controller;
use App\Classes\Constants\ServiceType;
use App\Classes\Constants\TasksAction;
use App\Controllers\Components\BlingProductSchedulerComponent;

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
    $blingUrls = $this->getServiceUrls(ServiceType::BLING);
    $integrationTasks = $this->integrationTaskModel->all();

    foreach ($integrationTasks as $key => $value):
      $integrationTasks[ $key ]['type'] = $this->getReferenceName($value['type']);
      $integrationTasks[ $key ]['service'] = $this->getServiceName($value['service']);
    endforeach;

    $this->view->assign('title', 'Página Inicial');
    $this->view->assign('successMessage', Flash::get('success'));
    $this->view->assign('errorMessage', Flash::get('error'));
    $this->view->assign('blingUrls', $blingUrls);
    $this->view->assign('integrationTasks', $integrationTasks);
    $this->view->render('index');
  }

  private function getServiceUrls(int $serviceType): array
  {
    $servicesUrls = [
      ServiceType::BLING => [
        ['url' => '/integration_tasks/suppliers/' . ServiceType::BLING . '/' . TasksAction::SEND, 'description' => 'Receber fornecedores'],
        ['url' => '/integration_tasks/categories/' . ServiceType::BLING . '/' . TasksAction::SEND, 'description' => 'Receber categorias'],
        ['url' => '/integration_tasks/products/' . ServiceType::BLING . '/' . TasksAction::SEND, 'description' => 'Receber produtos'],
      ],
    ];

    return $servicesUrls[ $serviceType ] ?? [];
  }

  public function suppliers(int $serviceType, int $taskStatus): void
  {
    if ($serviceType === ServiceType::BLING and $taskStatus === TasksAction::SEND) {
      $this->redirect('/integration_tasks', 'success', '0 fornecedores recebidos');
    }
  }

  public function categories(int $serviceType, int $taskStatus): void
  {
    if ($serviceType === ServiceType::BLING and $taskStatus === TasksAction::SEND) {
      $this->redirect('/integration_tasks', 'success', '0 categorias recebidas');
    }
  }

  public function products(int $serviceType, int $taskStatus): void
  {
    if ($serviceType === ServiceType::BLING and $taskStatus === TasksAction::SEND) {
      $blingScheduleSync = new BlingProductSchedulerComponent($this->integrationTaskModel);

      $result = $blingScheduleSync->scheduleSync();

      if (isset($result['error']) or ! isset($result['total_scheduled'])) {
        $this->redirect('/integration_tasks', 'error', 'Não foi possível receber produtos');
      }

      $this->redirect('/integration_tasks', 'success', $result['total_scheduled'] . ' produtos recebidos');
    }
  }
}