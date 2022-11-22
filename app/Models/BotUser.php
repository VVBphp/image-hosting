<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BotUser extends Model
{
    protected $table = 'bot_user';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'bot_user_id');
    }
}
