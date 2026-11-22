@props(['status'])

@php
    $colors = match ($status->value) {
        'wacana' => 'bg-[#f5e8cc] text-[#614418]',
        'akan_datang' => 'bg-[#e3ecef] text-[#264d5b]',
        'berjalan' => 'bg-[#ddede1] text-[#24543c]',
        'selesai' => 'bg-[#e8e8e1] text-[#484a40]',
    };
@endphp

<span {{ $attributes->class(['inline-flex w-fit rounded-full px-3 py-1 text-sm font-semibold', $colors]) }}>{{ $status->label() }}</span>
