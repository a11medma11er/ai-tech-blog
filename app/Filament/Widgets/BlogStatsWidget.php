<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Posts', Post::count())
                ->description('All posts in the system')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Published Posts', Post::where('is_published', true)->count())
                ->description('Currently published')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Draft Posts', Post::where('is_published', false)->count())
                ->description('Unpublished drafts')
                ->descriptionIcon('heroicon-o-pencil')
                ->color('warning'),

            Stat::make('Categories', Category::where('is_active', true)->count())
                ->description('Active categories')
                ->descriptionIcon('heroicon-o-tag')
                ->color('info'),
        ];
    }
}
