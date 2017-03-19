<?php

namespace Laprimavera\nominatim;

class nominatim
{

    private $reverse_addr = 'http://localhost/nominatim/reverse.php?format=#FORMAT#&lat=#LAT#&lon=#LNG#&zoom=#ZOOM#&addressdetails=#ADDR#';

    public function reversePoint($lat, $lng, $format = 'json', $zoom = 18, $addressdetails = 1)
    {
        $search = ['#LAT#', '#LNG#', '#FORMAT#', '#ZOOM#', '#ADDR#'];
        $replace = [$lat, $lng, $format, $zoom, $addressdetails];
        $url = \str_replace($search, $replace, $this->reverse_addr);
        $result = \json_decode(\file_get_contents($url));

        if (isset($result->address)) {
            return $result->address;
        } elseif (isset($result->error)) {
            return false;
        } else {
            return false;
        }
    }

}
