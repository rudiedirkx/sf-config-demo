<?php

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

require 'vendor/autoload.php';
require 'configuration.php';

$defaultConfig = Yaml::parse(file_get_contents('default.yml'));
$overrideConfig = Yaml::parse(file_get_contents('override.yml'));

echo '<pre>';

$processor = new Processor();
$configuration = new MyConfiguration();
try {
    $processedConfiguration = $processor->processConfiguration($configuration, [$defaultConfig, $overrideConfig]);
    print_r($processedConfiguration);
}
catch (InvalidConfigurationException $ex) {
    echo $ex->getMessage();
}
