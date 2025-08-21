<?php


namespace App\Support;

use Illuminate\Support\Str;


class Sanitize
{
    public static function toHtml(string $input): string
    {
        $html = Str::markdown($input, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        return strip_tags($html, '<p><a><strong><em>
                <ul>
                    <ol>
                        <li><code>
                                <pre><blockquote><br><hr>');
    }
}
