<?php

namespace App\Controllers;

use App\Classes\Flash;
use App\Helpers\TypeHelper;
use App\Controllers\Controller;
use App\Helpers\ConversionHelper;
use App\Models\IntegrationLogModel;

class IntegrationLogsController extends Controller
{
  protected $layout = 'default';
  protected $folder = 'integration_logs';

  private $integrationLogModel;

  public function __construct()
  {
    parent::__construct();

    $this->integrationLogModel = new IntegrationLogModel();
  }

  public function index(): void
  {
    $perPage = 10;
    $currentPage = intval($_GET['page'] ?? 1);

    $columns = [
      'id',
      'type',
      'service_from',
      'service_to',
      'reference_id',
      'created_at',
      'updated_at',
    ];

    $integrationLogs = $this->integrationLogModel->allPagination($currentPage, $perPage, $columns, 'DESC');

    $integrationLogsTotal = $this->integrationLogModel->count();
    $this->view->assign('totalLogs', $integrationLogsTotal);

    $currentPage = max($currentPage, 1);
    $this->view->assign('currentPage', $currentPage);

    $totalPages = (int) ceil($integrationLogsTotal / $perPage);
    $this->view->assign('totalPages', $totalPages);

    foreach ($integrationLogs as $key => $value):
      $integrationLogs[ $key ]['type'] = TypeHelper::getReferenceName($value['type']);
      $integrationLogs[ $key ]['service_from'] = TypeHelper::getServiceName($value['service_from']);
      $integrationLogs[ $key ]['service_to'] = TypeHelper::getServiceName($value['service_to']);
    endforeach;

    $this->view->assign('title', 'Integration Logs');
    $this->view->assign('successMessage', Flash::get('success'));
    $this->view->assign('errorMessage', Flash::get('error'));
    $this->view->assign('neutralMessage', Flash::get('neutral'));
    $this->view->assign('integrationLogs', $integrationLogs);
    $this->view->render('index');
  }

  public function show(int $id)
  {
    $columns = [
      'id',
      'request_body',
      'response_body',
    ];

    $integrationLog = $this->integrationLogModel->find($id, $columns);

    $id = $integrationLog['id'] ?? '';
    $requestBody = $integrationLog['request_body'] ?? '';
    $responseBody = $integrationLog['response_body'] ?? '';

    $this->view->assign('title', 'Integration Log');
    $this->view->assign('successMessage', Flash::get('success'));
    $this->view->assign('errorMessage', Flash::get('error'));
    $this->view->assign('neutralMessage', Flash::get('neutral'));
    $this->view->assign('id', $id);
    $this->view->assign('requestBody', ConversionHelper::formatterJson($requestBody));
    $this->view->assign('responseBody', ConversionHelper::formatterJson($responseBody));
    $this->view->render('log');
  }
}