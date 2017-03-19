<?php

/**
 * @author Grzegorz Galas
 */

namespace Laprimavera\scope;

use Laprimavera\company;
use Laprimavera\cache;
use Laprimavera\gps;

/**
 * SOAP class
 */
final class soap
{

    protected $db;
    protected $company;
    protected $cache;
    protected $translator;

    /**
     * 
     * @param scope\iface\db $db
     * @param cache\helper\cacheManager $cache
     * @param company\companyRow $company
     */
    public function __construct(\SerwerPaliwowy\db\iface\db $db, cache\helper\cacheManager $cache, company\companyRow $company)
    {
        $this->db = $db;
        $this->cache = $cache;
        $this->company = $company;
    }

    /**
     * Save colection
     * 
     * @param object[] $data
     */
    public function sendAllData($data)
    {
        foreach ($data as $v) {
            $this->sendOneData($v);
        }
        return true;
    }

    /**
     * Save one item to db
     * 
     * @param object $data
     * @return boolean
     */
    public function sendOneData($data)
    {

        $dane = (array) $data;

        $redis_cache_key = sprintf(\Laprimavera\conf::get('REDIS_CACHE_KEY'), $this->company->id, $dane['objId']);

        $cached = $this->cache->get(cache\helper\REDIS, $redis_cache_key);
        if ($cached && $cached['generated'] == $dane['measurementDate']) {
            $dane['errors'] += 256; // duplicate
            return true;
        }

        if (!$dane['lng'] && !$dane['lat']) {
            $dane['errors'] += 128; // wrong signal
        }
      
        $insert = [
            'row_id' => $dane['rowId'],
            'obj_id' => $dane['objId'],
            'vehicle_name' => (string) $dane['vehicleName'],
            'generator_power' => $dane['generatorPower'],
            'engine_rpm' => $dane['engineRpm'],
            'fuel_temperature' => $dane['fuelTemperature'],
            'generated' => $dane['measurementDate'],
            'speed' => round(($dane['speed'] < 1.6) ? 0 : $dane['speed']),
            'direction' => (int) $dane['motionDirection'],
            'lng' => $dane['lng'],
            'lat' => $dane['lat'],
            'power_status' => (int) $dane['powerStatus'],
            'error' => $dane['errors'],
            'src_company' => $this->company->id,
        ];

        try {
            $this->db->insert('master.gps', $insert);
            $insert['id'] = $this->db->lastInsertId();
            $this->cache->set(cache\helper\REDIS, $redis_cache_key, $insert);

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

}
