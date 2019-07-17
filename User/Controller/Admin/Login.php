<?php
namespace User\Controller\Admin;

use \Core\Controller\Base as BaseController;

use \Acl\Model\Acl;

class Login extends BaseController
{
    public $layout = 'admin/layout/admin.php';

    public function listAction() {
        return $this->loginAction();
    }

    public function loginAction()
    {
        //check auth
        $acl = Acl::getInstance();
        $auth = (bool) $acl->getUserAdmin();

        //try to auth
        if($this->request->isPost()) {
            $data = $this->request->post();
            $auth = $acl->authUserAdmin([
                'user' => $data['usr'],
                'pass' => $data['pw'],
            ]);
        }

        //redirect if logged in
        if($auth) {
            $this->app->redirect($acl->getRedirectUrl('/admin'));
            exit;
        }

        //render form if not logged in
        $this->app->render('login.php');
    }
}