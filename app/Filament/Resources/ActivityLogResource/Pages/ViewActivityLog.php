<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use Filament\Infolists;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class ViewActivityLog extends ViewRecord
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->schema([
                Section::make('Log Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('description'),
                        Infolists\Components\TextEntry::make('causer.name')->label('User'),
                        Infolists\Components\TextEntry::make('subject_type')
                            ->label('Record Type')
                            ->formatStateUsing(fn (string $state): string => Str::afterLast($state, '\\')),
                        Infolists\Components\TextEntry::make('created_at')->label('Log Time')->dateTime('F j, Y, g:i a'),
                    ])->columns(2),

                Section::make('Logged Data')
                    ->schema(function (?Activity $record): array {
                        if (! $record || $record->properties->isEmpty()) {
                            return [];
                        }

                        $event = $record->event;
                        $properties = $record->properties->toArray();

                        // Helper to build schema for a set of properties
                        $buildSchema = function (array $data, string $prefix): array {
                            $schema = [];
                            foreach ($data as $key => $value) {
                                if ($key === 'is_active') {
                                    $schema[] = Infolists\Components\IconEntry::make("{$prefix}.is_active")
                                        ->label(ucfirst(str_replace('_', ' ', $key)))
                                        ->boolean();
                                } else {
                                    $schema[] = Infolists\Components\TextEntry::make("{$prefix}.{$key}")
                                        ->label(ucfirst(str_replace('_', ' ', $key)));
                                }
                            }

                            return $schema;
                        };

                        if ($event === 'updated') {
                            return [
                                Grid::make(2)
                                    ->schema([
                                        Section::make('Old Values')
                                            ->schema($buildSchema($properties['old'] ?? [], 'properties.old')),
                                        Section::make('New Values')
                                            ->schema($buildSchema($properties['attributes'] ?? [], 'properties.attributes')),
                                    ]),
                            ];
                        }

                        $dataKey = $event === 'deleted' ? 'old' : 'attributes';
                        $label = match ($event) {
                            'created' => 'Created Data',
                            'deleted' => 'Deleted Data',
                            default => 'Data'
                        };

                        return [
                            Section::make($label)
                                ->schema($buildSchema($properties[$dataKey] ?? [], "properties.{$dataKey}"))
                                ->columns(2),
                        ];
                    })
                    ->visible(fn (?Activity $record) => $record && $record->properties->isNotEmpty()),
            ]);
    }
}
