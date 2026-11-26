@extends('layouts.public')
@section('content')
    <x-page-heading title="Antrean Layanan" eyebrow="Ruang kerja pengurus" description="Kelola pengajuan masuk, tindak lanjut, dan riwayat status secara privat." />
    <div class="site-shell em-service-layout">
        <a class="button-secondary" href="{{ route('office.index') }}">Kembali ke sekretariat</a>
        @if(session('status'))<p class="em-service-note" role="status">{{ session('status') }}</p>@endif
        @if($errors->any())<div role="alert" class="em-service-note">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        <nav class="em-desk-metrics" aria-label="Filter antrean">
            <a class="em-service-card" href="{{ route('desk.queue') }}" @if(!$status) aria-current="page" @endif><strong>{{ $counts->sum() }}</strong><span>Semua</span></a>
            @foreach($statuses as $key=>$label)
                <a class="em-service-card" href="{{ route('desk.queue',['status'=>$key]) }}" @if($status===$key) aria-current="page" @endif><strong>{{ $counts[$key] ?? 0 }}</strong><span>{{ $label }}</span></a>
            @endforeach
        </nav>
        @forelse($tickets as $ticket)
            <article class="em-tool-card">
                <p class="em-kicker">{{ $ticket->reference }} · {{ $statuses[$ticket->status] }}</p>
                <h2 class="section-title">{{ $categories[$ticket->category] }}</h2>
                <p><strong>Pemohon:</strong> {{ $ticket->applicant }}</p>
                <p><strong>Kontak:</strong> {{ $ticket->contact ?: 'Tidak dicantumkan' }}</p>
                <p class="em-prewrap">{{ $ticket->description }}</p>
                <details><summary>Riwayat internal</summary><ol class="em-desk-timeline">@foreach($ticket->events->sortBy('id') as $event)<li><strong>{{ $statuses[$event->status] }}</strong><time>{{ $event->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</time>@if($event->internal_note)<p class="em-prewrap">{{ $event->internal_note }}</p>@endif</li>@endforeach</ol></details>
                @if(count(\App\Models\EkrafServiceRequest::TRANSITIONS[$ticket->status]))
                    <form class="em-admin-form" method="post" action="{{ route('desk.update',$ticket) }}">@csrf @method('PATCH')
                        <input type="hidden" name="expected_status" value="{{ $ticket->status }}">
                        <label>Status berikutnya<select name="status">@foreach(\App\Models\EkrafServiceRequest::TRANSITIONS[$ticket->status] as $next)<option value="{{ $next }}">{{ $statuses[$next] }}</option>@endforeach</select></label>
                        <label>Catatan internal (tidak tampil ke pemohon)<textarea name="internal_note" maxlength="1500" rows="3"></textarea></label>
                        <button class="button-primary">Perbarui status</button>
                    </form>
                @endif
            </article>
        @empty
            <p class="em-service-note">Belum ada pengajuan pada antrean ini.</p>
        @endforelse
        {{ $tickets->links() }}
    </div>
@endsection
