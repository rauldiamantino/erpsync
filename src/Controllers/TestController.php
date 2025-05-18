<?php

namespace App\Controllers;

use App\Controllers\Components\BlingProductSchedulerComponent;
use App\Controllers\Controller;
use App\Models\QueueModel;

class TestController extends Controller
{
  protected $folder = 'Test';
  protected $layout = 'test';

  public function __construct()
  {
    parent::__construct();
  }

  public function index(): void
  {
    // Always render before testing
    $this->view->assign('title', "Page's Tests");
    $this->view->render('index');

    $queueModel = new QueueModel();
    $blingScheduleSync = new BlingProductSchedulerComponent($queueModel);
    $result = $blingScheduleSync->scheduleSync();

    pr($result);
  }
}
