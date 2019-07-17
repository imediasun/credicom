<?php

namespace Admin\Block\Menu;

use Core\Block\Base as BaseBlock;
use \Acl\Model\Acl;

class Main extends BaseBlock {
    public $template = 'menu/main.php';
    public $cache = false;
    public $cacheTimout = 86400; //24h
    
    public function getCacheKey() {
        $user = Acl::getInstance()->getUserAdmin();
        $userId = ($user) ? $user->getId() : 'guest';
        return sprintf('%s_%s', parent::getCacheKey(), $userId);
    }
    
    public function init()
    {
        $menu = $this->getMenu();

        $this->setViewData(array(
            'menu' => $menu,
        ));
    }
    
    public function getMenu()
    {
        $user = Acl::getInstance()->getUserAdmin();
        $result = $this->getCompatibilityMenu(); //old verison with really strange perission check

        $result = array_merge([
            [
                'url' => '/admin/',
                'title' => 'Start',
                'visible' => (bool) $user,
            ],
            [
                'url' => '/admin/cron/manager/list',
                'title' => 'Cron',
                'visible' => (bool) $user,
            ],
            [
                'url' => '/admin/credit-request/tests',
                'title' => 'Tests',
                'visible' => (bool) $user,
            ],
        ], $result);

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $currentMenuItemKey = 0;
        foreach($result as $k => $v) {
            if(isset($v['url']) && strpos($path, $v['url'])) $currentMenuItemKey = $k;
        }
        $result[$currentMenuItemKey]['current'] = true;
        
        return $result;
    }

    public function getCompatibilityMenu()
    {
        $result = [];

        $user = Acl::getInstance()->getUserAdmin();

        $modulesCollection = \Core\Collection\Base::getInstance([
            'table' => 'be_modules'
        ]);
        $modules = $modulesCollection->getList(['sort' => 'sort']);

        foreach($modules as $module){
            if(!file_exists(BASE_DIR."/admin/modules/admin/".$module['path'])) continue; //module does not exist
            if(!$user['bereich'.$module['id']]==1) continue; //access check

            $result[] = [
                'url' => '/admin/index.php?loadCmsModule='.$module['id'],
                'title' => $module['caption'],
            ];
        }

        return $result;
    }
}
