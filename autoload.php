<?php
require_once dirname(__FILE__) . '/init.php';
function __autoload($class_name) {
	if (file_exists(dirname(__FILE__).'/models/'.$class_name.'.php')) {
        require_once(dirname(__FILE__) .'/models/'.$class_name.'.php');
    } else if (file_exists(dirname(__FILE__) .'/controllers/'.$class_name.'.php')) {
        require_once(dirname(__FILE__) .'/controllers/'.$class_name.'.php');
    }else {
        echo "Can't find class ".$class_name;
    }
	//var_dump($class_name);
    //include '/models/'.$class_name.'.php';
}