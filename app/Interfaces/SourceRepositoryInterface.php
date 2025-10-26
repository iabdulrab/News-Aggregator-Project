<?php

namespace App\Interfaces;

interface SourceRepositoryInterface
{
    public function getAllWithCount();
    
    public function findByIdWithCount(int $id);
}

