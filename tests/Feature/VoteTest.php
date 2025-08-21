<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Thread, Board, AnonSession, Vote};
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_upserts_a_vote_and_updates_score(): void
    {
        $board = Board::factory()->create();
        $anon = AnonSession::factory()->create(['session_hash' => 'x']);
        $thread = Thread::create([
            'board_id' => $board->id,
            'anon_session_id' => $anon->id,
            'content' => 'hi',
        ]);

        Vote::updateOrCreate(
            [
                'votable_type' => Thread::class,
                'votable_id'   => $thread->id,
                'anon_session_id' => $anon->id,
            ],
            ['value' => 1]
        );

        $thread->recalcScore();

        $this->assertEquals(1, $thread->score);
    }
}
