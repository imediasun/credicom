<?php

namespace Core\Slim;

use \Acl\Model\Acl;

class Slim extends \RKA\Slim {

    public function initDatabase()
    {
        $config = \Core\Model\Registry::getInstance()->getConfig();
        $this->db = new \MySQL(
           $config['mysql']['server'],
           $config['mysql']['username'],
           $config['mysql']['password'],
           $config['mysql']['database']
        );
    }
    
    public function initAcl()
    {
        $this->acl = Acl::getInstance();
    }

    public function createControllerClosure($name)
    {
        list($controllerName, $actionName) = explode(':', $name);
        if(!class_exists($controllerName)) {
            die(sprintf('Class %s does not exist', $controllerName));
        }
        
        return parent::createControllerClosure($name);
    }
    
    public function render($template, $data = array(), $status = null)
    {
        if($this->request->isAjax()) {
            $this->renderAjax($template, $data, $status);
            return;
        }

        $this->response->header('Content-Type','text/html;charset=utf-8');
        parent::render($template, $data, $status);
    }

    public function renderAjax($template, $data = array(), $status = null)
    {
        $this->view->enableLayout = false;
        $this->view->appendData($data);
        $result = array(
            'html' => $this->view->render($template),
        );

        $this->response->header('Content-Type','application/json;charset=utf-8');
        $this->response->body(json_encode($result));
    }
}