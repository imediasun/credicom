<?php
namespace App\modules\Admin\Controller;

use \App\modules\Core\Controller\Base as BaseController;

abstract class Base extends BaseController {
    public $useAcl = true;
    public $layout = 'admin/layout/admin.php';

    public function init()
    {
        parent::init();

        $user = $this->app->acl->getUserAdmin();
        if(!$user) {
            $this->app->acl->setRedirectUrl($this->app->request->getResourceUri());
            $callbackName = '\User\Controller\Admin\Login:loginAction';
            $callback = $this->app->createControllerClosure($callbackName);
            call_user_func_array($callback, []);
            exit;
        }
    }
}