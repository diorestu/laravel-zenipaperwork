<?php

use App\Models\AppNotification;
use App\Models\Invoice;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('paperwork:invoice-reminders', function () {
    Invoice::whereIn('status', ['sent', 'partial'])
        ->whereDate('due_date', '<=', now()->addDays(3)->toDateString())
        ->each(function (Invoice $invoice): void {
            AppNotification::firstOrCreate([
                'company_id' => $invoice->company_id,
                'title' => 'Invoice due: '.$invoice->number,
            ], [
                'body' => 'Balance due Rp '.number_format((float) $invoice->balance_due, 0, ',', '.'),
            ]);
        });

    Invoice::dueForReminder()
        ->with('company')
        ->each(function (Invoice $invoice): void {
            AppNotification::firstOrCreate([
                'company_id' => $invoice->company_id,
                'title' => 'Invoice overdue: '.$invoice->number,
            ], [
                'body' => 'Sisa tagihan Rp '.number_format((float) $invoice->balance_due, 0, ',', '.').' lewat jatuh tempo '.$invoice->due_date->format('d M Y').'.',
            ]);
            $invoice->update(['last_reminder_at' => now()]);
        });

    $this->info('Paperwork invoice reminders generated.');
})->purpose('Generate Paperwork invoice due & overdue reminders');

Schedule::command('paperwork:invoice-reminders')->dailyAt('08:00');
