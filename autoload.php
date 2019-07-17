<?php

spl_autoload_register(function ($class) 
{
    $path = str_replace('\\', '/', $class);

    $filePath = BASE_DIR . '/application/modules/'.  $path .'.php';

    if(!file_exists($filePath)) return;
    
    include $filePath;
});