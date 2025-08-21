<?php

namespace App\Support;


class Anon
{
    public static function handleForThread(int $threadId, int $anonSessionId): array
    {
        $h = hash_hmac('sha256', $threadId . '|' . $anonSessionId, config('app.key'));
        $num = hexdec(substr($h, 0, 6)) % 10000; // 0-9999
        $colorIdx = hexdec(substr($h, 6, 2)) % 8; // 0-7
        return [
            'name' => 'Anon-' . $num,
            'color' => $colorIdx,
        ];
    }
}
