{{-- resources/views/components/vote.blade.php --}}
@props([
    /** @var \Illuminate\Database\Eloquent\Model $model */
    'model',
    /** 'thread'|'post'|'comment'|'reply' */
    'type' => 'thread',
    /** -1|0|+1; jika tidak dipassing, kita coba resolve dari sesi */
    'myVote' => null,
])

@php
    // Resolve myVote jika tidak dipassing dari view
    if ($myVote === null) {
        $anonKey  = session('anon_key');
        $existing = method_exists($model, 'currentViewerVote') ? $model->currentViewerVote($anonKey) : null;
        $myVote   = $existing ? (int) $existing->value : 0;
    }
@endphp

<div
    x-data="voteComponent({
        initialScore: {{ (int) $model->score }},
        initialMyVote: {{ (int) $myVote }},
        votableType: '{{ $type }}',
        votableId: {{ (int) $model->getKey() }},
        endpoint: '{{ route('vote.store') }}',
        csrf: '{{ csrf_token() }}',
    })"
    class="inline-flex items-center gap-2 text-base"
>
    <button
        type="button"
        @click="cast(1)"
        :disabled="loading"
        :aria-pressed="myVote === 1"
        :class="[
            'h-8 w-8 rounded-full flex items-center justify-center border border-black/5 dark:border-white/10',
            loading ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer',
            myVote === 1
                ? 'bg-emerald-600 text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-white/10 dark:text-gray-200 dark:hover:bg-white/20'
        ]"
        title="Upvote"
        aria-label="Upvote"
    >▲</button>

    <span class="min-w-[2.5rem] text-center font-semibold" x-text="score"></span>

    <button
        type="button"
        @click="cast(-1)"
        :disabled="loading"
        :aria-pressed="myVote === -1"
        :class="[
            'h-8 w-8 rounded-full flex items-center justify-center border border-black/5 dark:border-white/10',
            loading ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer',
            myVote === -1
                ? 'bg-rose-600 text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-white/10 dark:text-gray-200 dark:hover:bg-white/20'
        ]"
        title="Downvote"
        aria-label="Downvote"
    >▼</button>
</div>

@once
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('voteComponent', (opts) => ({
    score: opts.initialScore,
    myVote: opts.initialMyVote,
    loading: false,

    async cast(val) {
      if (this.loading) return;
      this.loading = true;

      // --- Optimistic UI ---
      const prevScore = this.score;
      const prevMyVote = this.myVote;

      let newScore = prevScore;
      if (this.myVote === val) {         // toggle off
        newScore = prevScore - val;
        this.myVote = 0;
      } else if (this.myVote === 0) {    // first time
        newScore = prevScore + val;
        this.myVote = val;
      } else {                           // switch -1 <-> +1
        newScore = prevScore + (val - this.myVote); // +2 atau -2
        this.myVote = val;
      }
      this.score = newScore;

      try {
        const res = await fetch(opts.endpoint, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': opts.csrf,
          },
          credentials: 'same-origin', // <- penting: kirim cookie sesi (CSRF & anon_key)
          body: JSON.stringify({
            votable_type: opts.votableType,
            votable_id: opts.votableId,
            value: val
          })
        });

        if (!res.ok) {
          const text = await res.text(); // log supaya tahu 419/422/500, dll
          console.error('Vote error', res.status, text);
          throw new Error('HTTP ' + res.status);
        }

        const data = await res.json();
        // Sinkron dengan server (jaga-jaga race-condition)
        if (typeof data.score !== 'undefined') this.score = data.score;
        if (typeof data.myVote !== 'undefined') this.myVote = data.myVote;

      } catch (e) {
        // Rollback jika gagal
        this.score = prevScore;
        this.myVote = prevMyVote;
        console.error('Vote failed:', e);
        alert('Gagal mengirim vote. Coba lagi.');
      } finally {
        this.loading = false;
      }
    }
  }));
});
</script>
@endonce
