<?php

/**
 * @author Grzegorz Galas
 */

namespace Laprimavera\queue\iface;

/**
 * interface for queue processor class
 *
 */
interface queueProcessor
{

    public function listen($queueName, $callback_class);

    public function insert($queueName, $message);

    public function queueDeclaration($queueName, $durable = false, $params = []);
}
