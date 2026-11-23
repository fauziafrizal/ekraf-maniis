@props(['role', 'position', 'member' => null, 'photo' => null, 'showPendingNote' => false])

<article data-leader-role="{{ $role }}" @class([
    'leadership-card',
    'leadership-card--chair' => $role === 'ketua',
    'leadership-card--pending' => ! $member,
    'leadership-card--portrait' => $photo,
])>
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

    @if ($photo)
        <x-public-photo :photo="$photo" class="leadership-photo" image-class="leadership-portrait" />
    @endif
</article>
