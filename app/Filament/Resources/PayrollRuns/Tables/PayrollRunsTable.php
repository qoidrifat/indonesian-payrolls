<?php

namespace App\Filament\Resources\PayrollRuns\Tables;

use App\Models\PayrollRun;
use App\Services\Payroll\PayrollWorkflow;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayrollRunsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period.code')->label('Period')->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('run_type')->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'       => 'gray',
                        'calculating' => 'warning',
                        'calculated'  => 'info',
                        'approved'    => 'success',
                        'paid'        => 'success',
                        'failed'      => 'danger',
                        default       => 'gray',
                    }),
                TextColumn::make('employee_count')->numeric()->sortable(),
                TextColumn::make('total_gross')->money('IDR', divideBy: 1)->sortable(),
                TextColumn::make('total_net')->money('IDR', divideBy: 1)->sortable(),
                TextColumn::make('total_tax')->money('IDR', divideBy: 1)->sortable(),
                TextColumn::make('calculated_at')->dateTime()->sortable(),
                TextColumn::make('approved_at')->dateTime()->sortable()->toggleable(),
                TextColumn::make('paid_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
                Action::make('calculate')
                    ->label('Calculate')
                    ->icon('heroicon-o-calculator')
                    ->visible(fn (PayrollRun $record) => in_array($record->status, ['draft', 'calculated', 'failed'], true))
                    ->requiresConfirmation()
                    ->action(function (PayrollRun $record, PayrollWorkflow $workflow) {
                        try {
                            $workflow->calculate($record);
                            Notification::make()
                                ->title('Payroll calculated')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            $record->update(['status' => 'failed']);
                            Notification::make()
                                ->title('Calculation failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (PayrollRun $record) => $record->status === 'calculated')
                    ->requiresConfirmation()
                    ->action(function (PayrollRun $record, PayrollWorkflow $workflow) {
                        $workflow->approve($record, auth()->id());
                        Notification::make()->title('Run approved')->success()->send();
                    }),
                Action::make('mark_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (PayrollRun $record) => $record->status === 'approved')
                    ->requiresConfirmation()
                    ->action(function (PayrollRun $record, PayrollWorkflow $workflow) {
                        $workflow->markPaid($record);
                        Notification::make()->title('Run marked paid')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
