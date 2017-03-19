<?php

namespace Laprimavera\queue;

/**
 * one of worker for queue
 * @author Grzegorz Galas
 */
class workerNominatim implements iface\workerCallback
{

    protected $nominatim = null;
    protected $cache = null;

    public function __construct(\Laprimavera\nominatim\nominatim $nominatim, \Laprimavera\cache\cacheRedis $cache)
    {
        $this->nominatim = $nominatim;
        $this->cache = $cache;
    }

    public function callback($msg)
    {
        $time_start = microtime(true);

        echo " [x] Received ", $msg->body, "\n";

        $code = $this->processing($msg->body);

        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

        $time_end = microtime(true);

        echo ' [x] Done [' . $code . '] - Total Execution Time: ', \number_format($time_end - $time_start, 3), 's', "\n";
    }

    public function processing($key)
    {

        $element = $this->cache->hashGet($key);
        if (count($element)) {

            if (!isset($element['lat']) || !isset($element['lng'])) {
                $reverse_location = false;
            } else {
                $reverse_location = $this->nominatim->reversePoint($element['lat'], $element['lng']);
            }
            
            if ($reverse_location) {
                if (isset($reverse_location->country_code)) {
                    $value['country_code'] = $reverse_location->country_code;
                } else {
                    $value['country_code'] = '';
                }
                if (isset($reverse_location->country)) {
                    $value['country'] = $reverse_location->country;
                } else {
                    $value['country'] = '';
                }
                if (isset($reverse_location->state)) {
                    $value['state'] = $reverse_location->state;
                } else {
                    $value['state'] = '';
                }
                if (isset($reverse_location->town)) {
                    $value['town'] = $reverse_location->town;
                } else {
                    $value['town'] = '';
                }
                if (isset($reverse_location->neighbourhood)) {
                    $value['neighbourhood'] = $reverse_location->neighbourhood;
                } else {
                    $value['neighbourhood'] = '';
                }
                if (isset($reverse_location->county)) {
                    $value['county'] = $reverse_location->county;
                } else {
                    $value['county'] = '';
                }
            } else {
                $value['country_code'] = '';
                $value['country'] = '';
                $value['state'] = '';
                $value['town'] = '';
                $value['neighbourhood'] = '';
                $value['county'] = '';
            }

            //rewrite cache
            $this->cache->hashSet($key, $value);

            return $value['country_code'];
        }
    }

}
