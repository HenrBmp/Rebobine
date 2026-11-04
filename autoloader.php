<?php
spl_autoload_register(function ($class) {
    $s = DIRECTORY_SEPARATOR;
    require __DIR__ . $s . $class . ".php";
});
