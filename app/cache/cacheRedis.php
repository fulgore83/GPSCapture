<?php

namespace Laprimavera\cache;

/**
 * class which use to manage redis cache
 * @author      Grzegorz Galas 
 */
class cacheRedis implements iface\redisCache
{

    private $redis = false;
    private $scheme = null;
    private $host = null;
    private $port = null;

    /**
     * create connection to redis server
     * 
     * @param string $host
     * @param int $port
     * @param string $scheme
     * @return boolean
     */
    public function __construct(string $host = '127.0.0.1', int $port = 6379, string $scheme = 'tcp')
    {

        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;

        return $this->connect();
    }

    /**
     * conect to redis server
     * 
     * @return boolean
     */
    private function connect() : bool
    {
        try {
            if ($this->redis) {
                return true;
            }

            $this->redis = new \Predis\Client([
                "scheme" => $this->scheme,
                "host" => $this->host,
                "port" => $this->port
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * save data to hash
     * 
     * @param string|int $key
     * @param array $values
     * @return boolean
     */
    public function hashSet(string $key, array $values) : bool
    {
        if (!$this->connect()) {
            return false;
        }

        if ($this->redis->hmset($key, $values)) {
            return true;
        }

        return false;
    }

    /**
     * get data from hash
     * 
     * @param string|int $key
     * @param string $field
     * @return string|boolean
     */
    public function hashGet(string $key, string $field = null)
    {
        if (!$this->connect()) {
            return false;
        }

        if ($field === null) {
            if (!$this->redis->exists($key)) {
                return false;
            }

            return $this->redis->hgetall($key);
        } else {
            if (!$this->redis->hexists($key, $field)) {
                return false;
            }

            return $this->redis->hget($key, $field);
        }
    }

    /**
     * delete hash from redis
     * 
     * @param string|int $key
     * @return boolean
     */
    public function hashDel(string $key) : bool
    {
        if (!$this->connect()) {
            return false;
        }

        if ($this->redis->del([$key])) {
            return true;
        }

        return false;
    }

    /**
     * get all matches key from redis
     * 
     * @param string $pattern
     * @return type
     */
    public function getKeys(string $pattern = '*') : array
    {
        return $this->redis->keys($pattern);
    }

    /**
     * get item
     * 
     * @param string $key
     * @return string
     */
    public function get(string $key) : string
    {
        return $this->redis->get($key);
    }

    /**
     * 
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function set(string $key, string $value) : bool
    {
        return $this->redis->set($key, $value);
    }

}
