<?php

namespace App\Filament\Resources\SalaryStructures;

use App\Filament\Resources\SalaryStructures\Pages\CreateSalaryStructure;
use App\Filament\Resources\SalaryStructures\Pages\EditSalaryStructure;
use App\Filament\Resources\SalaryStructures\Pages\ListSalaryStructures;
use App\Filament\Resources\SalaryStructures\Schemas\SalaryStructureForm;
use App\Filament\Resources\SalaryStructures\Tables\SalaryStructuresTable;
use App\Models\SalaryStructure;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalaryStructureResource extends Resource
{
    protected static ?string $model = SalaryStructure::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Struktur Gaji';

    protected static ?string $modelLabel = 'Struktur Gaji';

    protected static ?string $pluralModelLabel = 'Struktur Gaji';

    public static function form(Schema $schema): Schema
    {
        return SalaryStructureForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalaryStructuresTable::configure($table);
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
            'index' => ListSalaryStructures::route('/'),
            'create' => CreateSalaryStructure::route('/create'),
            'edit' => EditSalaryStructure::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
