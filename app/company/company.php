<?php

/**
 * @author Grzegorz Galas
 */

namespace Laprimavera\company;

/**
 * company managed
 *
 */
final class company
{

    /**
     *
     * @var pdo
     */
    private $db;

    /**
     * 
     * @param db\iface\db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * autoryzation and return company record from databse
     * 
     * @param string $company
     * @param string $passwd
     * @return company\companyRow|false
     */
    public function authenticate($company, $passwd)
    {
        $s = $this->db->prepare('SELECT * FROM company WHERE login = ? AND passwd = ?');
        $s->execute([$company, $passwd]);
        return $s->fetchObject(companyRow::class);
    }

}
