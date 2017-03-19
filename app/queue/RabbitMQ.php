<?php

namespace Laprimavera\queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class to manage rabbit queues
 * @author Grzegorz Galas
 */
class RabbitMQ implements iface\queueProcessor
{

    protected $connect = null;
    protected $channel = null;

    protected $host = null;
    protected $port = null;
    protected $user = null;
    protected $pass = null;
    protected $vhost = null;
    
    /**
     * simple constructor
     * 
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $pass
     * @param string $vhost
     * @return object
     */
    public function __construct(string $host = 'localhost', int $port = 5672, string $user = 'guest', string $pass = 'guest', string $vhost = '/')
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->vhost = $vhost;

        return $this->connect();
    }

    /**
     * connect to rabbit server
     * 
     * @return boolean
     */
    private function connect()
    {
        try {
            $this->connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->pass, $this->vhost);
            $this->channel = $this->connection->channel();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * listener
     * 
     * @call nohup php filename.php &
     * 
     * @param string $queueName
     * @
     */
    public function listen($queueName, $callback_class)
    {
        \set_time_limit(0);
        \ignore_user_abort(1);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($queueName, '', false, false, false, false, [$callback_class, 'callback']);

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    public function insert($queueName, $message)
    {
        $msg = new AMQPMessage($message);
        $this->channel->basic_publish($msg, '', $queueName);
    }

    public function queueDeclaration($queueName, $durable = false, $params = [])
    {
        $this->channel->queue_declare($queueName, false, $durable, false, false, false, $params);
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

}
