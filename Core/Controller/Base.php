<?php
namespace App\modules\Core\Controller;

use \App\modules\Core\Model\Registry;

abstract class Base
{
    // Optional properties
    protected $app;
    protected $request;
    protected $response;
    
    public $useSession = true;
    public $useAcl = false;

    public $layout = null;


    // Optional setters
    public function setApp($app)
    {
        $this->app = $app;

        $this->view = $this->app->view();
//        $view->setTemplatesDirectory($templatesPath);
        
        $class = explode('\\',get_class($this), 3);
        $this->view->module = $class[0];
        $this->view->controller = strtolower(str_replace('\\', '/', $class[2]));
        
        $this->view->setData(array(
            'cfg' => Registry::getInstance()->getConfig(),
        ));

        $this->app->initDatabase();
        if($this->useAcl) {
            $this->app->initAcl();
        }

        if($this->layout) {
            $this->app->view->set('layout', $this->layout);
        }
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    // Init
    public function init()
    {
        // do things now that app, request and response are set.
    }
    
    public function addMessage($text, $type)
    {
        $messages = isset($_SESSION['page_messages']) ? $_SESSION['page_messages'] : array();
        $messages[] = array(
            'text' => $text,
            'type' => strtolower($type),
        );
        $_SESSION['page_messages'] = $messages;
    }
    
    public function renderAjaxMessage($text, $type)
    {
        $this->addMessage($text, $type);
        echo json_encode(array(
            'messages' => $this->view->render('core/helpers/messages.php'),
        ));
    }
    
    public function __destruct()
    {
//        \DatabaseDebug::renderDebug();
    }
}