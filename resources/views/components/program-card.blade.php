@props(['program', 'heading' => 'h2'])

<article class="flex h-full min-w-0 flex-col items-start rounded-2xl border border-[#dfe4d8] bg-white p-6 sm:p-7">
    <x-program-status :status="$program->status" />
    <{{ $heading }} class="mt-5 text-xl font-semibold leading-snug tracking-tight wrap-anywhere text-pine sm:text-2xl">
        <a class="underline decoration-[#9ab3a4] underline-offset-4 hover:decoration-current" href="{{ route('programs.show', $program->slug) }}">{{ $program->title }}</a>
    </{{ $heading }}>
    <p class="mt-3 leading-relaxed wrap-anywhere text-[#416656]">{{ $program->summary }}</p>
    <dl class="mt-5 border-t border-[#e5e9df] pt-4">
        <dt class="text-sm font-semibold text-pine">Sasaran</dt>
        <dd class="mt-1 leading-relaxed wrap-anywhere text-[#416656]">{{ $program->audience }}</dd>
    </dl>
</article>
