<?php

namespace Sigma\Controller\Admin;

use \Admin\Controller\Base as BaseAdminController;
use \Sigma\Grid\Admin\Reply as GridReply;


class Reply extends BaseAdminController {

    public function listAction()
    {
        //TODO : http://www.ok-soft-gmbh.com/jqGrid/OK/CustomActionButton.htm
        $grid = new GridReply();

        $this->view->appendData(array(
            'gridConfig' => $grid->getJavaScriptCode(),
        ));

        $this->app->render('list.php');
    }

    public function gridDataAction()
    {
        $grid = new GridReply();
        $grid->printRespositoryData();
        exit;
    }

}
