<?php

namespace App\Services\Interfaces;

interface ContentGeneratorInterface
{
    public function generateArticle(array $trendData): array;
    public function generateFeaturedImage(string $prompt): ?string;
}
