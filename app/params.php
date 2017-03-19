<?php

namespace Laprimavera;

# DB connection
const MYSQL_HOST = '10.0.0.1';
const MYSQL_DB = 'gps';
const MYSQL_USER = 'quest';
const MYSQL_PASS = 'quest';

# memcache
const MEMCACHE_HOST = "localhost";
const MEMCACHE_PORT = 11211;

# redis
const REDIS_HOST = 'redis';
const REDIS_PORT = 6379;
const REDIS_SCHEME = 'tcp';

const REDIS_CACHE_KEY = 'r_%s_%s'; // src_company, object_id
const REDIS_RELATIONS = 'rr_$s'; //local_id

const RABBITMQ_HOST = 'rabbitmq';
const RABBITMQ_PORT = 5672;
const RABBITMQ_USER = 'guest';
const RABBITMQ_PASS = 'guest';
const RABBITMQ_VHOST = '/';
