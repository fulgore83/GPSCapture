<?php

namespace Laprimavera\queue\helper;

/**
 * @author Grzegorz Galas
 */
abstract class queueOrganizer
{

    protected $qp;
    protected $wc;
    protected $queueName = 'example.queue.name';

    public function __construct(\Laprimavera\queue\iface\queueProcessor $qp, \Laprimavera\queue\iface\workerCallback $wc = null)
    {
        $this->qp = $qp;
        $this->wc = $wc;
    }

    public function listen()
    {
        $this->queueDeclaration();
        $this->qp->listen($this->queueName, $this->wc);
    }

    public function insert($msg)
    {
        $this->queueDeclaration();
        $this->qp->insert($this->queueName, $msg);
    }

    private function queueDeclaration()
    {
        //set standard params - queue live to 120s
        $params = [
            "x-message-ttl" => ['I', 120 * 1000],
            "x-expires" => ['I', 120 * 1000 + 1000],
        ];

        $this->qp->queueDeclaration($this->queueName, false, $params);
    }

}
