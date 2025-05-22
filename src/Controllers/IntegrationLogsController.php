<?php

namespace App\Controllers;

use App\Classes\Flash;
use App\Models\IntegrationLogModel;
use App\Controllers\Controller;
use App\Helpers\TypeHelper;

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
    $integrationLogs = $this->integrationLogModel->all();

    foreach ($integrationLogs as $key => $value):
      $integrationLogs[ $key ]['type'] = TypeHelper::getReferenceName($value['type']);
      $integrationLogs[ $key ]['service'] = TypeHelper::getServiceName($value['service']);
      $integrationLogs[ $key ]['request_body'] = $this->formatterJson($value['request_body']);
      $integrationLogs[ $key ]['response_body'] = $this->formatterJson($value['response_body']);
    endforeach;

    $this->view->assign('title', 'Integration Logs');
    $this->view->assign('successMessage', Flash::get('success'));
    $this->view->assign('errorMessage', Flash::get('error'));
    $this->view->assign('neutralMessage', Flash::get('neutral'));
    $this->view->assign('integrationLogs', $integrationLogs);
    $this->view->render('index');
  }

  public function formatterJson($json)
  {
    $array = json_decode($json);
    return json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  }
}