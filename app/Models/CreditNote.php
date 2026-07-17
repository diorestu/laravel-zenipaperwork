<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CreditNote extends Model
{
    use BelongsToCompany;
    use HasFactory;

    protected $fillable = [
        'company_id', 'invoice_id', 'client_id', 'number',
        'issue_date', 'amount', 'reason', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CreditNote $note): void {
            $note->number ??= static::generateNumber($note->company_id);
        });
    }

    public static function generateNumber(int $companyId): string
    {
        do {
            $candidate = 'CN-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        } while (static::forCompany($companyId)->where('number', $candidate)->exists());

        return $candidate;
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
