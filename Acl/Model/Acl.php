<?php
namespace App\modules\Acl\Model;

use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Core\Model\Traits\Singleton as TraitSingleton;

use \App\modules\User\Collection\UserAdmin as CollectionUserAdmin;

class Acl extends BaseModel
{
    use TraitSingleton;

    public function __construct(array $items = array())
    {
        parent::__construct($items);
        $this->init();
    }

    public function init() {
        $this->initUserAdmin();
    }

    public function initUserAdmin()
    {
        $isAuthenticated = (isset($_SESSION['auth']) && $_SESSION['auth']);
        $userId = isset($_SESSION['adminid']) ? $_SESSION['adminid'] : null;
        if(!$isAuthenticated || !$userId) return;

        $user = CollectionUserAdmin::getInstance()->load($userId);
        if(!$user) return;

        $this->setUserAdmin($user);
    }

    public function authUserAdmin($data) {
        $result = false;

        $user = \User\Collection\UserAdmin::getInstance()->load(['filter' => [
            'login' => $data['user'],
            'password' => md5($data['pass']),
            'aktiv' => 1,
        ]]);
        if(!$user) return $result;

        $result = true;
        $_SESSION['auth']= true;
        $_SESSION['adminid'] = $user->getId();

        return $result;
    }

    /* Redirect URL*/
    public function setRedirectUrl($url) {
        $_SESSION['acl_redirect_url'] = $url;
        return $this;
    }
    public function getRedirectUrl($default = null) {
        return isset($_SESSION['acl_redirect_url']) ? $_SESSION['acl_redirect_url'] : $default;
    }
}


