<?php

/**
 * @author Grzegorz Galas
 */

namespace Laprimavera;

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 0);

include __DIR__ . '/app/Autoloader.php';
Autoloader::register();

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    scope\auth::throwUnauthorized();
    return false;
}

$dsn = 'mysql:host=' . conf::get('MYSQL_HOST') . ';dbname=' . conf::get('MYSQL_DB');
try {
    $db = new db\pdo($dsn, conf::get('MYSQL_USER'), conf::get('MYSQL_PASS'));
    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (\Exception $e) {
    return false;
}

$company = new company\company($db);
$companyRow = $company->authenticate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

if (!$companyRow) {
    scope\auth::throwUnauthorized();
    return false;
}

$wsdl = $companyRow->wsdl . '.wsdl';
if (isset($_GET['wsdl'])) {
    \header('Content-Type: text/xml');
    \readfile('wsdl/' . $wsdl);
    return;
} else {
    //$memcache = new cache\cacheMem(conf::get('MEMCACHE_HOST'), conf::get('MEMCACHE_PORT'));
    $redis = new cache\cacheRedis(
            conf::get('REDIS_HOST'),
            conf::get('REDIS_PORT'),
            conf::get('REDIS_SCHEME')
            );
    $rabbit = new queue\RabbitMQ(
            conf::get('RABBITMQ_HOST'),
            conf::get('RABBITMQ_PORT'),
            conf::get('RABBITMQ_USER'),
            conf::get('RABBITMQ_PASS'),
            conf::get('RABBITMQ_VHOST')
            );
    $nominatimOrganizer = new queue\helper\nominatimOrganizer($rabbit);
    $cache = new cache\helper\cacheManager($redis, null, $db, $nominatimOrganizer);
    $soap = new scope\soap($db, $cache, $companyRow);
    $server = new \SoapServer('wsdl/' . $wsdl);
    $server->setObject($soap);
    $server->handle();
}
