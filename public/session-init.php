<?php

use Symfony\Component\Yaml\Yaml;

// autoloading composer
require_once __DIR__."/../vendor/autoload.php";

// démarrage de la session
session_start();

// instanciation de la config
$config = Yaml::parseFile(__DIR__."/../config/config.yaml");

$_SESSION["mail_username"] = $config["mail_username"];
$_SESSION["mail_password"] = $config["mail_password"];