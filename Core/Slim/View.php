<?php
namespace Core\Slim;

use \Slim\LayoutView;

class View extends LayoutView {
    public $module;
    public $controller;
    public $enableLayout = true;
    
    /**
     * Get fully qualified path to template file using templates base directory
     * @param  string $file The template file pathname relative to templates base directory
     * @return string
     */
    public function getTemplatePathname($file)
    {
        $fileParts = explode('/',$file, 3);
        $module = (count($fileParts) == 3) ? ucfirst($fileParts[0]) : $this->module;
        $controller = (count($fileParts) == 3) ? $fileParts[1] : ((count($fileParts) == 2) ? $fileParts[0] : $this->controller);
        $file = end($fileParts);

        $path = BASE_DIR .'/application/modules'. DIRECTORY_SEPARATOR . $module. DIRECTORY_SEPARATOR .'Views'. DIRECTORY_SEPARATOR . $controller . DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);
    
        return $path;
    }
    
    /**
     * make render function public
     */
    public function renderTemplate($template, $data = null)
    {
        $debugTimeStart = microtime(true); 

        $result = parent::render($template, $data);

        $debugTimeEnd = microtime(true);
        if(defined("DEBUG_MODE") && DEBUG_MODE) {
			\cerTemplateDebug::$debug[] = array(
				'time' => ($debugTimeEnd-$debugTimeStart),
                'timeStart' => $debugTimeStart,
                'timeEnd'=> $debugTimeEnd,
				'template' => $this->getTemplatePathname($template),
				'backtrace'	=> \cerDatabaseDebug::debugBacktrace(),
			);
		}
        
        return $result;
    }

    public function fetch($template, $data = null)
    {
        if($this->enableLayout) 
        {
            $result = parent::fetch($template, $data);
            return $result;
        }
            
        return $this->renderTemplate($template, $data);
    }
}