<?php
namespace User\Controller\Admin;

use \Admin\Controller\Base as BaseAdminController;
use \User\Grid\Admin\ActivityLog as GridActivityLog;



class ActivityLog extends BaseAdminController
{

    public function listAction()
    {
        //TODO : http://www.ok-soft-gmbh.com/jqGrid/OK/CustomActionButton.htm
        $grid = new GridActivityLog();

        $this->view->appendData(array(
            'gridConfig' => $grid->getJavaScriptCode(),
        ));

        $this->app->render('list.php');
    }

    public function gridDataAction()
    {
        $grid = new GridActivityLog();
        $grid->printRespositoryData();
        exit;
    }

}