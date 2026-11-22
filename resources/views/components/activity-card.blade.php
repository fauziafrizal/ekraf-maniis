@props(['activity', 'cover' => null, 'heading' => 'h2'])

<article class="h-full min-w-0 overflow-hidden rounded-2xl border border-[#dfe4d8] bg-white">
    @if ($cover)
        <x-public-photo :photo="$cover" image-class="aspect-[16/10] w-full object-cover" />
    @endif
    <div class="p-6 sm:p-7">
        <time datetime="{{ $activity->event_date->toDateString() }}" class="text-sm font-semibold text-[#416656]">{{ $activity->event_date->translatedFormat('j F Y') }}</time>
        <{{ $heading }} class="mt-3 text-xl font-semibold leading-snug tracking-tight wrap-anywhere text-pine sm:text-2xl">
            <a class="underline decoration-[#9ab3a4] underline-offset-4 hover:decoration-current" href="{{ route('activities.show', $activity->slug) }}">{{ $activity->title }}</a>
        </{{ $heading }}>
        <p class="mt-3 leading-relaxed wrap-anywhere text-[#416656]">{{ $activity->summary }}</p>
    </div>
</article>
