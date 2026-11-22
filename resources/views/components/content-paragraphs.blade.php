@props(['text'])

<div {{ $attributes->class(['prose-copy space-y-4 wrap-anywhere']) }}>
    @foreach (preg_split('/\R\s*\R/', trim($text), flags: PREG_SPLIT_NO_EMPTY) as $paragraph)
        <p class="whitespace-pre-line">{{ $paragraph }}</p>
    @endforeach
</div>
