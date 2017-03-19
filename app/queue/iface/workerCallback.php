<?php

/**
 * @author Grzegorz Galas
 */

namespace Laprimavera\queue\iface;

/**
 * interface worker callback
 *
 */
interface workerCallback
{

    public function callback($msg);
}
