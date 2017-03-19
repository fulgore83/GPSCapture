<?php

/**
 * @author Grzegorz Galas
 */

namespace Laprimavera;

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 0);
error_reporting(E_ALL);
ini_set('display_errors', 'On');


if (php_sapi_name() !== 'cli') {
    echo 'Call this cript from CLI!\n';
    return;
}

include __DIR__ . '/app/Autoloader.php';
Autoloader::register();

$rabbit = new queue\RabbitMQ(
        conf::get('RABBITMQ_HOST'),
        conf::get('RABBITMQ_PORT'),
        conf::get('RABBITMQ_USER'),
        conf::get('RABBITMQ_PASS'),
        conf::get('RABBITMQ_VHOST')
        );
$nominatim = new nominatim\nominatim();
$redis = new cache\cacheRedis(
        conf::get('REDIS_HOST'),
        conf::get('REDIS_PORT'),
        conf::get('REDIS_SCHEME')
        );
$nominatim_callback = new queue\workerNominatim($nominatim, $redis);

$qo = new queue\helper\nominatimOrganizer($rabbit, $nominatim_callback);
$qo->listen();
