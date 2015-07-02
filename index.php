<?php

// short constant for directory separator
define('DS' , DIRECTORY_SEPARATOR);

// get the path of our main folder
define('ROOT' , realpath(dirname(__FILE__)) . DS);

// require startup file
require ROOT . 'vendor' . DS . 'startup.php';