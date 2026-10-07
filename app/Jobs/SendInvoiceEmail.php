<?php

namespace App\Jobs;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInvoiceEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invoice $invoice)
    {
    }

    public function handle(): void
    {
        $this->invoice->loadMissing('client');

        $email = trim((string) $this->invoice->client?->email);

        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        // Cegah alamat dengan sintaks tidak valid (misal diawali '-' atau format salah)
        if (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9._%+-]*@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            Log::warning("Skipping invoice email: invalid recipient syntax [{$email}] for invoice ID {$this->invoice->id}");
            return;
        }

        try {
            Mail::to($email)->send(new InvoiceMail($this->invoice));
        } catch (\Throwable $e) {
            Log::error("Failed to send invoice email to [{$email}] for invoice ID {$this->invoice->id}: ".$e->getMessage());
        }
    }
}
