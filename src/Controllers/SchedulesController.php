<?php

namespace App\Controllers;

use App\Helpers\Flash;
use App\Models\ScheduleModel;
use App\Controllers\Controller;
use App\Classes\Constants\ServiceType;
use App\Classes\Constants\ScheduleStatus;
use App\Controllers\Components\BlingProductSchedulerComponent;

class SchedulesController extends Controller
{
  protected $layout = 'default';
  protected $folder = 'schedules';

  private $scheduleModel;

  public function __construct()
  {
    parent::__construct();

    $this->scheduleModel = new ScheduleModel();
  }

  public function index(): void
  {
    $blingUrls = $this->getServiceUrls(ServiceType::BLING);
    $schedules = $this->scheduleModel->all();

    foreach ($schedules as $key => $value):
      $schedules[ $key ]['service'] = $this->getServiceName($value['service']);
    endforeach;

    $this->view->assign('title', 'Página Inicial');
    $this->view->assign('successMessage', Flash::get('success'));
    $this->view->assign('errorMessage', Flash::get('error'));
    $this->view->assign('blingUrls', $blingUrls);
    $this->view->assign('schedules', $schedules);
    $this->view->render('index');
  }

  private function getServiceName(?int $serviceType): string
  {
    $services = [
      ServiceType::BLING => 'Bling',
    ];

    return $services[ $serviceType ] ?? 'Desconhecido';
  }

  private function getServiceUrls(int $serviceType): array
  {
    $servicesUrls = [
      ServiceType::BLING => [
        ['url' => '/schedules/suppliers/' . ServiceType::BLING . '/' . ScheduleStatus::SEND, 'description' => 'Receber fornecedores'],
        ['url' => '/schedules/categories/' . ServiceType::BLING . '/' . ScheduleStatus::SEND, 'description' => 'Receber categorias'],
        ['url' => '/schedules/products/' . ServiceType::BLING . '/' . ScheduleStatus::SEND, 'description' => 'Receber produtos'],
      ],
    ];

    return $servicesUrls[ $serviceType ] ?? [];
  }

  public function suppliers(int $serviceType, int $scheduleStatus): void
  {
    if ($serviceType === ServiceType::BLING and $scheduleStatus === ScheduleStatus::SEND) {
      $this->redirect('/schedules', 'success', '0 fornecedores agendado(s)');
    }
  }

  public function categories(int $serviceType, int $scheduleStatus): void
  {
    if ($serviceType === ServiceType::BLING and $scheduleStatus === ScheduleStatus::SEND) {
      $this->redirect('/schedules', 'success', '0 categorias agendada(s)');
    }
  }

  public function products(int $serviceType, int $scheduleStatus): void
  {
    if ($serviceType === ServiceType::BLING and $scheduleStatus === ScheduleStatus::SEND) {
      $blingScheduleSync = new BlingProductSchedulerComponent($this->scheduleModel);

      $result = $blingScheduleSync->scheduleSync();

      if (isset($result['error']) or ! isset($result['total_scheduled'])) {
        $this->redirect('/schedules', 'error', 'Erro ao agendar produtos');
      }

      $this->redirect('/schedules', 'success', $result['total_scheduled'] . ' produtos agendado(s)');
    }
  }
}