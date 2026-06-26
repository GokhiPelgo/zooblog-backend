<?php

namespace App\Filament\Resources\Tutorials\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TutorialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required()
                    ->rule('regex:/^[a-z0-9-]+$/')
                    ->validationMessages([
                        'regex' => 'El slug solo acepta minúsculas, números y guiones — nada de espacios, acentos ni signos como ?¿.',
                    ])
                    ->helperText('Solo minúsculas, números y guiones. Ej: como-cuidar-un-perro'),
                TextInput::make('lang')
                    ->required()
                    ->default('es'),
                Textarea::make('excerpt')
                    ->columnSpanFull(),
                RichEditor::make('content')
                    ->columnSpanFull(),
                FileUpload::make('cover_image')
                    ->image()
                    ->disk(config('filesystems.tutorials_disk')),
                TextInput::make('level'),
                Toggle::make('is_published')
                    ->required(),
                DateTimePicker::make('published_at'),
            ]);
    }
}
