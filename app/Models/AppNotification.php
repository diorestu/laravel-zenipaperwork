<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use BelongsToCompany;

    protected $table = 'notifications';

    protected $fillable = ['company_id', 'user_id', 'title', 'body', 'read_at'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }
}
