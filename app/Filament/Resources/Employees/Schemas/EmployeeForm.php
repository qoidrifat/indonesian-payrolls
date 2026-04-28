<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('employee_number')
                    ->required(),
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                DatePicker::make('date_of_birth'),
                TextInput::make('gender'),
                TextInput::make('marital_status')
                    ->required()
                    ->default('single'),
                TextInput::make('religion'),
                TextInput::make('citizenship')
                    ->required()
                    ->default('WNI'),
                Select::make('department_id')
                    ->relationship('department', 'name'),
                Select::make('position_id')
                    ->relationship('position', 'name'),
                DatePicker::make('hire_date')
                    ->required(),
                DatePicker::make('end_date'),
                TextInput::make('employment_type')
                    ->required()
                    ->default('permanent'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                TextInput::make('workweek')
                    ->required()
                    ->default('five_days'),
            ]);
    }
}
