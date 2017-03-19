<?php

/**
 * @author Grzegorz Galas
 */

namespace Laprimavera\cache\iface;

/**
 * interfejs obsługi cache
 *
 */
interface kvCache
{

    public function get(string $key) : bool;

    public function set(string $key, string $value) : bool;
}
