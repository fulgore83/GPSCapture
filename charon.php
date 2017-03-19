<?php

/**
 * @author Grzegorz Galas
 */

namespace Laprimavera;

include __DIR__ . '/app/Autoloader.php';
Autoloader::register();

$redis = new cache\cacheRedis(
        conf::get('REDIS_HOST'),
        conf::get('REDIS_PORT'),
        conf::get('REDIS_SCHEME')
        );
/* fast json decode answer */
echo json_encode($redis->hashGet($_GET['id']));
