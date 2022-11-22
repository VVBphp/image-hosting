<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nette\Utils\Random;

class Image extends Model
{
    use SoftDeletes;

//    protected $with = ['user'];
    protected $appends = ['link'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s'];

    public function scopeCreatedBetween(Builder $query, $start, $end): Builder
    {
        return $query->whereDate('created_at', '>=', Carbon::parse($start))
            ->whereDate('created_at', '<=', Carbon::parse($end));
    }


    public function getLinkAttribute()
    {
        return route('images.show', $this->code);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function generateCode()
    {
        do {
            $code = Random::generate(10, '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ');
        } while (Image::whereCode($code) !== null);
        return $code;
    }

    public function removeExif($old, $new, $mime)
    {
        // Open the input file for binary reading
        $f1 = fopen($old, 'rb');
        // Open the output file for binary writing
        $f2 = fopen($new, 'wb');

        // Find EXIF marker
        while (($s = fread($f1, 2))) {
            $word = @unpack('ni', $s)['i'];
            if ($word == 0xFFE1 && $mime == 'image/jpeg') {
                // Read length (includes the word used for the length)
                $s   = fread($f1, 2);
                $len = @unpack('ni', $s)['i'];
                // Skip the EXIF info
                fread($f1, $len - 2);
                break;
            } else {
                fwrite($f2, $s, 2);
            }
        }

        // Write the rest of the file
        while (($s = fread($f1, 4096))) {
            fwrite($f2, $s, strlen($s));
        }

        fclose($f1);
        fclose($f2);
    }
}
