<?php

namespace Laprimavera\cache\iface;

/**
 * @author      Grzegorz Galas
 * @description interface for redis cache
 */
interface redisCache
{

    public function hashSet(string $key, array $values) : bool;

    public function hashGet(string $key, string $field = null);

    public function hashDel(string $key) : bool;

    public function set(string $key, string $value) : bool;

    public function get(string $key) : string;
}
