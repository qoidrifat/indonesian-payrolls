<?php

namespace App\Filament\Resources\PayrollRuns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PayrollRunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('payroll_period_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('run_type')
                    ->required()
                    ->default('monthly'),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                TextInput::make('employee_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_gross')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_net')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_tax')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_bpjs_employee')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_bpjs_employer')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('approved_by')
                    ->numeric(),
                DateTimePicker::make('approved_at'),
                DateTimePicker::make('calculated_at'),
                DateTimePicker::make('paid_at'),
                Textarea::make('meta')
                    ->columnSpanFull(),
            ]);
    }
}
