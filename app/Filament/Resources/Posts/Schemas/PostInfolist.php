<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Schema;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')
                    ->label('Title')
                    ->size('lg')
                    ->weight('bold')
                    ->columnSpanFull(),

                TextEntry::make('slug')
                    ->label('Slug')
                    ->copyable()
                    ->badge(),

                IconEntry::make('is_published')
                    ->label('Publication Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextEntry::make('published_at')
                    ->label('Published At')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Not published'),

                ImageEntry::make('featured_image')
                    ->label('Featured Image')
                    ->columnSpanFull(),

                TextEntry::make('content')
                    ->label('Content')
                    ->html()
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime('d/m/Y H:i'),

                TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->columns(2);
    }
}
