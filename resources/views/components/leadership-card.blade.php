@props(['role', 'position', 'member' => null, 'photo' => null, 'showPendingNote' => false, 'hanging' => false])

<article data-leader-role="{{ $role }}" @class([
    'leadership-card',
    'leadership-card--chair' => $role === 'ketua',
    'leadership-card--pending' => !$member,
    'leadership-card--portrait' => $photo,
    'em-hanging-card' => $hanging,
])>
    @if ($hanging && $photo)
        <div class="em-hanging-stage" data-hanging-stage>
            <span class="em-hanging-pin" aria-hidden="true"></span>
            <div class="em-hanging-pendulum" data-hanging-portrait>
                <span class="em-hanging-rope" aria-hidden="true"></span>
                <span class="em-hanging-frame">
                    <span class="em-hanging-clip" aria-hidden="true"></span>
                    <span class="em-hanging-image">
                        <img draggable="false" src="{{ $photo['url'] }}" alt="{{ $photo['alt'] }}" aria-describedby="hanging-help-{{ $role }}" width="{{ $photo['width'] }}" height="{{ $photo['height'] }}" decoding="async">
                    </span>
                    <span class="em-hanging-tag">{{ $position }}</span>
                </span>
            </div>
            <span id="hanging-help-{{ $role }}" class="sr-only">Foto pengurus bergerak otomatis secara perlahan dan tidak dapat ditarik menggunakan pointer, mouse, atau touchpad.</span>
        </div>
    @endif
    <div class="leadership-content">
        @if ($member)
            <p class="leadership-position">{{ $position }}</p>
            <h3 class="leadership-name">{{ $member->name }}</h3>
            @if (filled($member->division))
                <p class="leadership-division">{{ $member->division }}</p>
            @endif
            @if (filled($member->bio))
                <x-content-paragraphs :text="$member->bio" class="leadership-bio" />
            @endif
        @else
            <h3 class="leadership-pending-title">{{ $position }}</h3>
            @if ($showPendingNote)
                <p class="leadership-pending">Profil sedang disiapkan.</p>
            @endif
        @endif
    </div>

    @if ($photo && !$hanging)
        <x-public-photo :photo="$photo" class="leadership-photo" image-class="leadership-portrait" />
    @endif
</article>
