<?php

namespace App\Repositories;

use App\Interfaces\SourceRepositoryInterface;
use App\Models\Source;

class SourceRepository implements SourceRepositoryInterface
{
    public function getAllWithCount()
    {
        return Source::withCount('articles')->get();
    }

    public function findByIdWithCount(int $id)
    {
        return Source::withCount('articles')->find($id);
    }
}

