<?php

namespace App\Services\Source;

use App\Interfaces\SourceRepositoryInterface;

class SourceService
{
    protected SourceRepositoryInterface $sourceRepository;

    public function __construct(SourceRepositoryInterface $sourceRepository)
    {
        $this->sourceRepository = $sourceRepository;
    }

    public function getAllSources()
    {
        return $this->sourceRepository->getAllWithCount();
    }

    public function getSourceById(int $id)
    {
        return $this->sourceRepository->findByIdWithCount($id);
    }
}

