<?php

namespace Laprimavera\scope;

/**
 * simple authorizaton static caller
 */
class auth
{

    public static function throwUnauthorized()
    {

        header('WWW-Authenticate: Basic realm="LP WebService"');
        header('HTTP/1.0 401 Unauthorized');
        echo "You must enter a valid login ID and password to access this resource\n";
        exit;
    }

}
