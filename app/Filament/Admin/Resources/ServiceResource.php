<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ServiceResource\Pages;
use App\Filament\Admin\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label(__('User'))
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable(),

                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('slug')->required(),
                Forms\Components\Textarea::make('description'),
                Forms\Components\TextInput::make('price')->required(),
                Forms\Components\FileUpload::make('image')->image()->disk('public')->imageEditor()->visibility('public')->required(),
                Forms\Components\FileUpload::make('icon')->image()->imageEditor()->visibility('public')->required(),
                Forms\Components\TimePicker::make('duration')
                    ->prefixIconColor('success'),

                Forms\Components\ToggleButtons::make('status')
                    ->label(__('Is Active'))
                    ->options([
                        'active' => __('Active'),
                        'suspended' => __('Suspended'),
                    ])
                    ->grouped()
                    ->boolean(),


                Forms\Components\ToggleButtons::make('is_vip')
                    ->label(__('Is Vip'))
                    ->grouped()
                    ->boolean(),

                Forms\Components\Repeater::make('preferences')->schema([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\TextInput::make('value')->required(),

                ]),

                Forms\Components\Textarea::make('notes'),


            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(__('ID'))->sortable(),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('description')->label(__('Description')),
                Tables\Columns\TextColumn::make('price')->label(__('Price')),
                Tables\Columns\TextColumn::make('duration')->label(__('Duration')),
                Tables\Columns\TextColumn::make('status')->label(__('Status')),
                Tables\Columns\TextColumn::make('is_vip')->label(__('Is VIP')),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created At')),
                Tables\Columns\TextColumn::make('updated_at')->label(__('Updated At')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
