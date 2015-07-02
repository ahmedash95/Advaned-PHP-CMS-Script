<?php

// get the application class
require ROOT . 'vendor' . DS . 'NPD' . DS  . 'Application.php';

// create a new object of application class
$app = new NPD\Application();
                            
// initialize main settings
$app->bootstrap();
