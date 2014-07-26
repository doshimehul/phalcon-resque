<?php

try {

    //Register an autoloader
    $loader = new \Phalcon\Loader();
    $loader->registerDirs(
        array(
            __DIR__.'/../app/background-jobs/'
        ), true
    )->register();

    //Create a DI
    $di = new Phalcon\DI\FactoryDefault();
}
catch(\Phalcon\Exception $e) {
    echo "PhalconException: ", $e->getMessage();
}
