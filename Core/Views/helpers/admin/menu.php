<?php
$menu = $this->data->get('menu');
if(!$menu) return;

$menuTitle = $menu['title'];
$menuLinks = $menu['links'];

include BASE_DIR."includes/elements/menu/config_menu_draw.php";


