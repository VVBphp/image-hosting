<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramApi extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'api_token';
}
