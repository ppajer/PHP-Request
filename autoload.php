<?php

spl_autoload_register(function($class) {
	require dirname(__FILE__)."/class.$class.php";
});

?>