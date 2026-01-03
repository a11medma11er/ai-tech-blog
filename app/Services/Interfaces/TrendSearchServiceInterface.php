<?php

namespace App\Services\Interfaces;

interface TrendSearchServiceInterface
{
    public function searchTrends(int $count = 5): array;
}
