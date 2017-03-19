<?php

/**
 * @author Grzegorz Galas
 */

namespace Laprimavera\cache;

/**
 * Class to manage memcaches
 *
 */
final class cacheMem implements iface\kvCache
{

    private $memcache = null;
    private $host = null;
    private $port = null;

    /**
     * Prepare variable for object to connect with memcache serwer
     * 
     * @param string $host
     * @param int $port
     */
    public function __construct($host = 'localhost', $port = 11211)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * connect with memcache serwer
     */
    private function connect()
    {
        $this->memcache = \memcache_connect($this->host, $this->port);
    }

    /**
     * Return data from memcache items
     * 
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return \memcache_get($this->memcache, $key);
    }

    /**
     * Save data to memcache item
     * 
     * @param string $key
     * @param mixed $value
     * @return this
     */
    public function set($key, $value)
    {
        \memcache_set($this->memcache, $key, $value, 0, 2592000);
        return $this;
    }

}
