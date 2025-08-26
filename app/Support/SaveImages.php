<?php

// app/Support/SaveImages.php
namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SaveImages
{
    public static function storeMany($files): array
    {
        $saved = [];
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) continue;

            $path = $file->store('uploads', 'public'); // storage/app/public/uploads/...
            [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];

            $saved[] = [
                'path'   => $path,
                'mime'   => $file->getMimeType(),
                'size'   => $file->getSize(),
                'width'  => $width,
                'height' => $height,
            ];
        }
        return $saved;
    }
}
