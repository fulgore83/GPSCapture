<?php

namespace Laprimavera\db\iface;

interface db
{
    public function insert(string $table, array $data);
}
