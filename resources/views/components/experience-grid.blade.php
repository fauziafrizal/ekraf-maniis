@props(['compact' => false])
@php
    $modules = [
        [
            'market',
            'Creative Marketplace',
            'Temukan karya dan produk lokal. Hubungi penjual secara langsung.',
            'creative.market',
            'M3 9h18l-2-5H5L3 9Zm0 0v3a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0V9M5 15v6h14v-6M10 21v-6h4v6',
            'sage',
        ],
        [
            'learn',
            'Ekraf Academy',
            'Asah keterampilan kreatif dan kemampuan usaha dari mana saja.',
            'creative.academy',
            'm2 8 10-5 10 5-10 5L2 8Zm4 2v7l6 3 6-3v-7M22 8v6',
            'gold',
        ],
        [
            'student',
            'Ekraf Pelajar',
            'Ruang eksplorasi ide dan proyek kreatif untuk pelajar.',
            'creative.students',
            'M12 3a7 7 0 0 0-4 13v2h8v-2a7 7 0 0 0-4-13ZM9 21h6M12 10v4',
            'clay',
        ],
        [
            'map',
            'Jelajahi Maniis',
            'Kenali lanskap, perikanan, dan potensi hasil kebun lokal.',
            'creative.map',
            'M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z M12 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z',
            'forest',
        ],
        [
            'directory',
            'Direktori Pelaku Ekraf',
            'Kenali para pelaku usaha kreatif yang dipublikasikan.',
            'creative.directory',
            'M4 4h16v16H4zM8 9h8M8 13h8M8 17h5',
            'sage',
        ],
        [
            'data',
            'Ekraf Data Center',
            'Lihat statistik portal berdasarkan data publik.',
            'creative.data',
            'M4 3v17h17M8 16v-4M13 16V7M18 16v-7',
            'gold',
        ],
        [
            'service',
            'Pusat Layanan',
            'Ajukan pendataan, pembinaan, dan kolaborasi.',
            'services',
            'M12 21 4 13a5 5 0 0 1 8-6 5 5 0 0 1 8 6l-8 8Z',
            'clay',
        ],
        [
            'help',
            'Pusat Bantuan',
            'Temukan panduan dan informasi penggunaan portal.',
            'creative.help',
            'M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20ZM9 9a3 3 0 1 1 4 3v2M13 17h.01',
            'forest',
        ],
    ];
    $cards = $compact ? array_slice($modules, 0, 4) : $modules;
@endphp
@if ($compact)
    <x-card-carousel id="home-service-cards" label="Layanan kreatif pilihan" :count="count($cards)" track-class="em-experience-grid em-home-service-track" :premium="true">
        <x-experience-cards :cards="$cards" />
    </x-card-carousel>
@else
    <div class="em-experience-grid">
        <x-experience-cards :cards="$cards" />
    </div>
@endif
