<?php

namespace Laprimavera;

require_once 'params.php';

class conf
{

    public static function get($param_name)
    {
        $param_name = strtoupper($param_name);
        $namespace = __NAMESPACE__;

        $const = $namespace . '\\' . $param_name;

        if (defined($const)) {
            return constant($const);
        } else {
            return null;
        }
    }

}
