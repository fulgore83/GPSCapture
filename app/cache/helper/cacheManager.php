<?php

namespace Laprimavera\cache\helper;

use Laprimavera\nominatim\nominatim;

const REDIS = 'redis';
const MEMCACHE = 'memcache';

/**
 * @author      Grzegorz Galas
 * @description consolidation for cache engine (REDIS, MEMCACHE),
 *              implements queues (RABBITMQ)
 * 
 */
class cacheManager
{

    private $memcache = null;
    private $redis = null;
    private $db = null;
    private $nominatimOrganizer = null;

    /**
     * 
     * @param \Laprimavera\cache\iface\redisCache $redis
     * @param \Laprimavera\cache\iface\kvCache $memcache
     * @param \Laprimavera\db\iface\db $db
     */
    public function __construct(\Laprimavera\cache\iface\redisCache $redis = null, \Laprimavera\cache\iface\kvCache $memcache = null, \Laprimavera\db\iface\db $db = null, \Laprimavera\queue\helper\nominatimOrganizer $nominatimOrganizer = null)
    {
        $this->redis = $redis;
        $this->memcache = $memcache;
        $this->db = $db;
        $this->nominatimOrganizer = $nominatimOrganizer;
    }

    public function set(string $system, string $key, array $value)
    {
        switch ($system) {
            case REDIS:
                /**
                 * dodawanie rozwiazywania punktów na kraje do kolejki
                 * nie tracimy czasu na dobijanie sie do nominatim po url
                 */
                if ($this->nominatimOrganizer && substr($key, 0, 2) == 'r_') {
                    $this->nominatimOrganizer->insert($key);
                }

                //$this->redis->hashDel($key); //gdy to nie jest ustawione REDIS dodaje nowe pola
                return $this->redis->hashSet($key, $value);
            case MEMCACHE:
                return $this->memcache->set($key, $value);
            default :
                return false;
        }
    }

    public function get(string $system, string $key)
    {
        switch ($system) {
            case REDIS:
                list(, $company, $device) = explode('_', $key);
                $table = 'master.gps';
                if ($company == 329) {
                    if ($device < 91000) {
                        $table = 'master.graveyard';
                    } else {
                        $table = 'master.heven';
                    }
                }

                $value = $this->redis->hashGet($key);
                if (!$value) {
                    $data = $this->getFromDb($key, $table);
                    if (!$data) {
                        return false;
                    }
                    $this->set(REDIS, $key, $data);
                }

                return $this->redis->hashGet($key);
            case MEMCACHE:
                $value = $this->memcache->get($key);
                if (!$value) {
                    $data = $this->getFromDb($key, 'master.dead');
                    if (!$data) {
                        return false;
                    }
                    $this->set(MEMCACHE, $key, $data);
                }

                return $this->memcache->get($key);
            default :
                return false;
        }
    }

    public function simpleSet(string $system, string $key, string $value)
    {
        switch ($system) {
            case REDIS:
                return $this->redis->set($key, $value);
            default :
                return false;
        }
    }

    public function simpleGet(string $system, string $key)
    {
        switch ($system) {
            case REDIS:
                return $this->redis->get($key);
            default :
                return false;
        }
    }

    private function getFromDb(string $key, string $table)
    {
        list(, $company, $device) = explode('_', $key);
        $stmt = $this->db->prepare('SELECT * FROM ' . $table . ' WHERE src_company = :company AND obj_id = :obj_id ORDER BY id DESC LIMIT 1');
        $stmt->execute([
            ':company' => $company,
            ':obj_id' => $device,
        ]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data) {
            return $data;
        }

        return false;
    }

}
