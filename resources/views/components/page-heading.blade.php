@props(['title', 'eyebrow' => null, 'description' => null])

<header {{ $attributes->class(['editorial-page-heading']) }}>
    <div class="site-shell py-14 sm:py-20">
        @if ($eyebrow)
            <p class="eyebrow">{{ $eyebrow }}</p>
        @endif
        <h1 class="page-title mt-3 max-w-4xl text-balance wrap-anywhere">{{ $title }}</h1>
        @if ($description)
            <p class="prose-copy mt-5 max-w-2xl wrap-anywhere">{{ $description }}</p>
        @endif
        @if ($slot->isNotEmpty())
            <div class="mt-7">{{ $slot }}</div>
        @endif
    </div>
</header>
