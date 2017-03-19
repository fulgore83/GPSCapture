<?php

/**
 * @author Grzegorz Galas
 */

namespace Laprimavera\db;

/**
 * DB adapter
 */
class pdo extends \PDO implements iface\db
{

    public function __construct(string $dsn, string $username = null, string $password = null, array $options = null)
    {
        parent::__construct($dsn, $username, $password, $options);
        
    }
    /**
     * Standart hydrate function for db
     * 
     * @param string $table
     * @param array|string[] $data
     * @return bool
     */
    public function insert(string $table, array $data)
    {
        $sql = 'INSERT INTO ' . $table . ' (' . implode(',', array_keys($data)) . ') VALUES (';
        $sql.= substr(str_repeat('?,', count($data)), 0, -1);
        $sql.= ')';

        return $this->prepare($sql)->execute(array_values($data));
    }

}
