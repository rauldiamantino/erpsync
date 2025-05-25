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
    $orderBy = 'DESC';
    $integrationLogs = $this->integrationLogModel->all($orderBy);

    foreach ($integrationLogs as $key => $value):
      $integrationLogs[ $key ]['type'] = TypeHelper::getReferenceName($value['type']);
      $integrationLogs[ $key ]['service_from'] = TypeHelper::getServiceName($value['service_from']);
      $integrationLogs[ $key ]['service_to'] = TypeHelper::getServiceName($value['service_to']);
      $integrationLogs[ $key ]['request_body'] = ConversionHelper::formatterJson($value['request_body']);
      $integrationLogs[ $key ]['response_body'] = ConversionHelper::formatterJson($value['response_body']);
    endforeach;

    $this->view->assign('title', 'Integration Logs');
    $this->view->assign('successMessage', Flash::get('success'));
    $this->view->assign('errorMessage', Flash::get('error'));
    $this->view->assign('neutralMessage', Flash::get('neutral'));
    $this->view->assign('integrationLogs', $integrationLogs);
    $this->view->render('index');
  }
}