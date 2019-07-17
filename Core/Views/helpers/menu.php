<ul class="menu">
<?php 
    foreach($menu as $menuItem) {
        if(isset($menuItem['visible']) && !$menuItem['visible']) continue;
?>
    <li <?= (isset($menuItem['current']) && $menuItem['current']) ? 'class="active"' : ''?>>
        <a href="<?= $menuItem['url']?>"><span class="label"><?= $menuItem['title']?></span></a>
        <?php if(isset($menuItem['children'])) { ?>
            <?= $this->render('core/helpers/menu.php', array('menu' => $menuItem['children'])) ?>
        <?php }?>
    </li>
<?php }?>
</ul>