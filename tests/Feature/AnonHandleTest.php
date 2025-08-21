<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Support\Anon;

class AnonHandleTest extends TestCase
{
    /** @test */
    public function it_produces_stable_pseudonym_per_thread(): void
    {
        $name1 = Anon::handleForThread(1, 77);
        $name2 = Anon::handleForThread(1, 77);

        $this->assertEquals($name1['name'], $name2['name']);
    }
}
