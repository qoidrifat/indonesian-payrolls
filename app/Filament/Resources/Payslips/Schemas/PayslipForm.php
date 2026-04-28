<?php

namespace App\Filament\Resources\Payslips\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PayslipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('payroll_item_id')
                    ->relationship('payrollItem', 'id')
                    ->required(),
                Select::make('employee_id')
                    ->relationship('employee', 'id')
                    ->required(),
                TextInput::make('payroll_period_id')
                    ->required()
                    ->numeric(),
                TextInput::make('payslip_number')
                    ->required(),
                TextInput::make('pdf_path'),
                TextInput::make('pdf_disk')
                    ->required()
                    ->default('local'),
                Textarea::make('snapshot')
                    ->required()
                    ->columnSpanFull(),
                DateTimePicker::make('generated_at'),
                DateTimePicker::make('sent_at'),
            ]);
    }
}
