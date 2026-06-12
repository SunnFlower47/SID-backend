<?php

namespace App\Contracts;

interface BukuAdministrasiInterface
{
    public function getData(array $filters, bool $isExport);
    public function getQuery(array $filters);
}
