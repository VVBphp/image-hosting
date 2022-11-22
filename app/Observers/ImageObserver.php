<?php

namespace App\Observers;

use App\Models\Image;
use Nette\Utils\Random;

class ImageObserver
{
    public function creating(Image $image): void
    {
        $image->code = $this->generateCode();
    }

    public function forceDeleted(Image $image)
    {
        // @TODO Удалить файлы
    }

    public function generateCode(): string
    {
        do {
            $code = Random::generate(10, '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ');
        } while (Image::whereCode($code)->first() !== null);
        return $code;
    }
}
