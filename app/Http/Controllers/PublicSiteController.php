<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\BoardMember;
use App\Models\CreativeListing;
use App\Models\GalleryPhoto;
use App\Models\Program;
use App\Models\Subsector;
use App\Support\PublicSite;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicSiteController extends Controller
{
    public function home(): View
    {
        $site = config('ekraf');
        $heroDetails = $site['assets']['hero'];
        $heroImage = $this->regionalPhoto($heroDetails);

        if ($heroImage === null) {
            $heroDetails = $site['assets']['hero_reference'] ?? [];
            $heroImage = $this->regionalPhoto($heroDetails);
        }

        $activities = Activity::recentlyPublished()->limit(3)->get();
        $galleryHighlights = GalleryPhoto::publiclyVisible()->inDisplayOrder()->lazy(24)
            ->map(fn (GalleryPhoto $photo) => [
                'image' => $this->galleryImage($photo),
                'caption' => $photo->caption,
            ])
            ->filter(fn (array $highlight) => $highlight['image'] !== null)
            ->take(3)
            ->collect();

        return $this->page('home', 'Beranda', $site['intro'], [
            'heroImage' => $heroImage,
            'heroPhotoDetails' => $heroDetails,
            'potentials' => $this->potentials(),
            'programs' => Program::recentlyPublished()->limit(3)->get(),
            'activities' => $activities,
            'activityCovers' => $this->activityCovers($activities),
            'galleryHighlights' => $galleryHighlights,
            'metaImage' => $heroImage,
        ]);
    }

    public function profile(): View
    {
        $members = BoardMember::published()->inDisplayOrder()->get();
        $membersByRole = $members->groupBy(function (BoardMember $member): string {
            $role = Str::slug($member->position);

            return $role === 'sekertaris' ? 'sekretaris' : $role;
        });
        $leadership = collect([
            'ketua' => 'Ketua',
            'wakil-ketua' => 'Wakil Ketua',
            'sekretaris' => 'Sekretaris',
        ])->map(fn (string $position, string $role) => [
            'position' => $position,
            'member' => $membersByRole->get($role)?->first(),
        ]);
        $featuredIds = $leadership->pluck('member.id')->filter();

        return $this->page('profile', 'Profil', 'Kenali organisasi dan struktur pengurus '.config('ekraf.short_name').' periode '.config('ekraf.period').'.', [
            'leadership' => $leadership,
            'hasPublishedLeaders' => $featuredIds->isNotEmpty(),
            'otherMembers' => $members->reject(fn (BoardMember $member) => $featuredIds->containsStrict($member->id)),
            'memberPhotos' => $members->mapWithKeys(fn (BoardMember $member) => [
                $member->id => PublicSite::image($member->photo_path, 'Foto '.$member->name),
            ]),
        ]);
    }

    public function maniis(): View
    {
        return $this->page('maniis', 'Potensi Maniis', config('ekraf.potential_intro'), [
            'potentials' => $this->potentials(),
        ]);
    }

    public function subsectors(): View
    {
        return $this->page('subsectors', 'Subsektor', 'Jelajahi subsektor ekonomi kreatif yang telah disetujui oleh pengurus '.config('ekraf.short_name').'.', [
            'subsectors' => Subsector::published()->inDisplayOrder()->get(),
        ]);
    }

    public function programs(): View
    {
        return $this->page('programs.index', 'Program', 'Lihat sasaran, status, dan informasi program '.config('ekraf.short_name').' yang telah diterbitkan.', [
            'programs' => Program::recentlyPublished()->get(),
        ]);
    }

    public function program(string $slug): View
    {
        $program = Program::published()->where('slug', $slug)->firstOrFail();

        return $this->page('programs.show', $program->title, $program->summary, [
            'program' => $program,
            'registrationUrl' => $program->registrationUrl(),
        ]);
    }

    public function activities(): View
    {
        $activities = Activity::recentlyPublished()->get();
        $photos = GalleryPhoto::publiclyVisible()->inDisplayOrder()->get();
        $photoAssets = $this->galleryImages($photos);

        return $this->page('activities.index', 'Kegiatan', 'Baca kabar kegiatan dan lihat dokumentasi foto '.config('ekraf.short_name').' yang telah diterbitkan.', [
            'activities' => $activities,
            'activityCovers' => $this->activityCovers($activities),
            'photos' => $photos->filter(fn (GalleryPhoto $photo) => isset($photoAssets[$photo->id])),
            'photoAssets' => $photoAssets,
        ]);
    }

    public function activity(string $slug): View
    {
        $activity = Activity::published()->where('slug', $slug)->firstOrFail();
        $photos = $activity->galleryPhotos()->published()->inDisplayOrder()->get();
        $photoAssets = $this->galleryImages($photos);
        $activityCovers = $this->activityCovers(collect([$activity]));

        return $this->page('activities.show', $activity->title, $activity->summary, [
            'activity' => $activity,
            'activityCovers' => $activityCovers,
            'photos' => $photos->filter(fn (GalleryPhoto $photo) => isset($photoAssets[$photo->id])),
            'photoAssets' => $photoAssets,
            'metaImage' => $activityCovers[$activity->id],
        ]);
    }

    public function hub(): View
    {
        return $this->page('hub.index', 'Smart Creative Hub', 'Portal digital ekonomi kreatif Kecamatan Maniis.');
    }

    public function marketplace(): View
    {
        return $this->creativeListings('product', 'Creative Marketplace', 'Jelajahi katalog produk lokal Maniis.');
    }

    public function creativeDirectory(): View
    {
        return $this->creativeListings('business', 'Direktori Pelaku Ekraf', 'Kenali pelaku usaha dan komunitas kreatif yang dipublikasikan.');
    }

    private function creativeListings(string $kind, string $title, string $description): View
    {
        $listings = CreativeListing::public()->where('kind', $kind)->orderBy('name')->get();

        return $this->page('hub.listings', $title, $description, compact('listings', 'kind'));
    }

    private function metrics(): array
    {
        return [
            'Usaha publik' => CreativeListing::public()->where('kind', 'business')->count(),
            'Produk publik' => CreativeListing::public()->where('kind', 'product')->count(),
            'Subsektor publik' => Subsector::published()->count(),
            'Program publik' => Program::published()->count(),
            'Kegiatan publik' => Activity::published()->count(),
            'Pengurus publik' => BoardMember::published()->count(),
        ];
    }

    public function dataCenter(): View
    {
        $villages = CreativeListing::public()->where('kind', 'business')
            ->selectRaw('village, count(*) as total')->groupBy('village')->orderByDesc('total')->get();

        return $this->page('hub.data', 'Ekraf Data Center', 'Statistik data publik Ekraf Maniis.', ['metrics' => $this->metrics(), 'villages' => $villages]);
    }

    public function dataCsv(): StreamedResponse
    {
        $metrics = $this->metrics();

        return response()->streamDownload(function () use ($metrics) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Indikator', 'Jumlah'], ',', '"', '');
            foreach ($metrics as $label => $value) {
                fputcsv($out, [$label, $value], ',', '"', '');
            }
            fclose($out);
        }, 'ekraf-maniis-statistik-publik.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function academy(): View
    {
        return $this->page('hub.learning', 'Ekraf Academy', 'Panduan praktis untuk pengembangan usaha kreatif.', [
            'students' => false, 'lessons' => [
                ['Validasi ide usaha', 'Kenali calon pelanggan, kebutuhan mereka, dan alternatif produk sebelum menentukan penawaran.', 'Wawancarai tiga calon pelanggan dan tulis kebutuhan yang paling sering muncul.'],
                ['Foto produk', 'Gunakan cahaya alami, latar bersih, dan beberapa sudut agar bentuk produk terlihat jelas.', 'Ambil tiga foto produk dengan sudut berbeda dan pilih satu yang paling informatif.'],
                ['Identitas merek', 'Tentukan pesan merek, warna, tipografi, dan kemasan yang konsisten.', 'Buat rancangan label yang menampilkan nama, manfaat, dan informasi produk yang benar.'],
                ['Harga dan biaya', 'Catat biaya bahan, tenaga, kemasan, serta pengeluaran lain sebelum menentukan harga.', 'Susun biaya satu unit produk dan bandingkan dengan harga jual yang direncanakan.'],
                ['Pemasaran digital', 'Pilih kanal yang digunakan calon pelanggan. Sampaikan manfaat produk secara jujur.', 'Tulis satu deskripsi produk serta rencana tiga konten promosi.'],
                ['Layanan pelanggan', 'Jawab pertanyaan dengan jelas, konfirmasi ketersediaan, dan catat umpan balik pelanggan.', 'Buat contoh balasan untuk pertanyaan harga, ketersediaan, dan keluhan.'],
            ],
        ]);
    }

    public function students(): View
    {
        return $this->page('hub.learning', 'Ekraf Pelajar Maniis', 'Ruang ide proyek pelajar dan kolaborasi sekolah.', [
            'students' => true, 'lessons' => [
                ['Cerita produk lokal', 'Kenali proses pembuatan sebuah produk dan manfaatnya bagi masyarakat.', 'Dengan izin pemilik, wawancarai pelaku usaha dan tulis cerita singkat.'],
                ['Desain kemasan', 'Perhatikan fungsi kemasan, keterbacaan label, dan bahan yang digunakan.', 'Buat sketsa dua alternatif kemasan dan jelaskan kelebihan masing-masing.'],
                ['Video promosi', 'Susun pembuka, isi, dan penutup singkat yang menampilkan keunggulan produk.', 'Buat storyboard video 30 detik. Minta izin sebelum merekam orang lain.'],
                ['Pameran karya', 'Susun hasil proyek dalam portofolio yang menjelaskan proses dan pembelajaran.', 'Buat satu halaman portofolio dengan judul, foto karya, dan refleksi.'],
            ],
        ]);
    }

    public function creativeMap(): View
    {
        return $this->page('hub.map', 'Peta dan Potensi Maniis', 'Kenali wilayah dan potensi lokal.', ['potentials' => $this->potentials()]);
    }

    public function toolkit(): View
    {
        return $this->page('hub.toolkit', 'Creative Business Toolkit', 'Alat bantu praktis pengembangan usaha.');
    }

    public function mySpace(): View
    {
        $listings = CreativeListing::public()->orderBy('name')->get();

        return $this->page('hub.my-space', 'Ruang Saya', 'Favorit dan progres belajar di perangkat Anda.', compact('listings'));
    }

    public function creativeCalendar(Request $request): View
    {
        $value = $request->query('bulan', now()->format('Y-m'));
        abort_unless(is_string($value) && preg_match('/^20[0-9]{2}-(0[1-9]|1[0-2])$/D', $value), 404);
        $month = Carbon::createFromFormat('!Y-m', $value, config('app.timezone'))->startOfMonth();
        $byDay = Program::published()->whereIn('status', ['akan_datang', 'berjalan', 'selesai'])
            ->where('starts_at', '>=', $month)->where('starts_at', '<', $month->copy()->addMonth())
            ->orderBy('starts_at')->get()->groupBy(fn (Program $program) => (int) $program->starts_at->format('j'));

        return $this->page('hub.calendar', 'Kalender Kreatif', 'Agenda program Ekraf Maniis.', [
            'month' => $month, 'byDay' => $byDay, 'previous' => $month->copy()->subMonth()->format('Y-m'), 'next' => $month->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function calendarEvent(string $slug): Response
    {
        $program = Program::published()->whereIn('status', ['akan_datang', 'berjalan', 'selesai'])->whereNotNull('starts_at')->where('slug', $slug)->firstOrFail();
        $escape = fn (string $text) => str_replace(['\\', ';', ',', "\r\n", "\r", "\n"], ['\\\\', '\\;', '\\,', '\\n', '\\n', '\\n'], $text);
        $lines = ['BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//Ekraf Maniis//Kalender Kreatif//ID', 'CALSCALE:GREGORIAN', 'BEGIN:VEVENT',
            'UID:ekraf-program-'.$program->id.'@ekraf-maniis',
            'DTSTAMP:'.now()->utc()->format('Ymd\\THis\\Z'),
            'DTSTART:'.$program->starts_at->copy()->utc()->format('Ymd\\THis\\Z'),
            'SUMMARY:'.$escape($program->title),
            'DESCRIPTION:'.$escape($program->summary.' | '.route('programs.show', $slug)),
            'LOCATION:'.$escape((string) $program->location), 'END:VEVENT', 'END:VCALENDAR'];
        $fold = function (string $line): string {
            $parts = [];
            while (strlen($line) > 75) {
                $part = mb_strcut($line, 0, 75, 'UTF-8');
                $parts[] = $part;
                $line = ' '.substr($line, strlen($part));
            }

            return implode("\r\n", [...$parts, $line]);
        };

        return response(implode("\r\n", array_map($fold, $lines))."\r\n", 200, [
            'Content-Type' => 'text/calendar; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="ekraf-program-'.$program->id.'.ics"',
        ]);
    }

    public function creativeHelp(): View
    {
        return $this->page('hub.help', 'Pusat Bantuan & Privasi', 'Panduan penggunaan portal dan privasi.');
    }

    public function services(): View
    {
        $agenda = Program::published()
            ->whereIn('status', ['akan_datang', 'berjalan'])
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')->get();

        $searchItems = collect([
            ['type' => 'Halaman', 'title' => 'Profil organisasi', 'description' => 'Tentang Ekraf Maniis dan pengurus.', 'url' => route('profile')],
            ['type' => 'Halaman', 'title' => 'Potensi Maniis', 'description' => 'Waduk Cirata, keramba jaring apung, curug, pegunungan, dan olahan buah lokal.', 'url' => route('maniis')],
            ['type' => 'Halaman', 'title' => 'Subsektor kreatif', 'description' => 'Ragam bidang ekonomi kreatif.', 'url' => route('subsectors')],
            ['type' => 'Halaman', 'title' => 'Program', 'description' => 'Informasi program dan pembinaan.', 'url' => route('programs.index')],
            ['type' => 'Halaman', 'title' => 'Kegiatan dan galeri', 'description' => 'Dokumentasi dan karya.', 'url' => route('activities.index')],
            ['type' => 'Alat usaha', 'title' => 'Creative Business Toolkit', 'description' => 'Rekomendasi pembinaan, titik impas, simulasi laba, caption, checklist usaha.', 'url' => route('creative.toolkit')],
            ['type' => 'Halaman', 'title' => 'Kalender Kreatif', 'description' => 'Agenda bulanan dan ekspor pengingat kalender.', 'url' => route('creative.calendar')],
            ['type' => 'Halaman', 'title' => 'Ruang Saya', 'description' => 'Favorit produk dan progres belajar.', 'url' => route('creative.space')],
            ['type' => 'Layanan', 'title' => 'Hubungi pengurus', 'description' => 'Pendataan UMKM, pengajuan pembinaan, pelatihan, kemitraan, dan usulan program.', 'url' => route('contact').'#form-kolaborasi'],
        ])->concat(Program::recentlyPublished()->get()->map(fn (Program $p) => [
            'type' => 'Program', 'title' => $p->title, 'description' => $p->summary, 'url' => route('programs.show', $p->slug),
        ]))->concat(Activity::recentlyPublished()->get()->map(fn (Activity $a) => [
            'type' => 'Kegiatan', 'title' => $a->title, 'description' => $a->summary, 'url' => route('activities.show', $a->slug),
        ]))->concat(Subsector::published()->inDisplayOrder()->get()->map(fn (Subsector $s) => [
            'type' => 'Subsektor', 'title' => $s->name, 'description' => $s->description, 'url' => route('subsectors').'#subsektor-'.$s->id,
        ]));

        return $this->page('services', 'Layanan', 'Akses pendataan, pengajuan pembinaan, agenda, dan informasi Ekraf Maniis.', compact('agenda', 'searchItems'));
    }

    public function contact(): View
    {
        return $this->page('contact', 'Kontak', 'Temukan kanal resmi pengurus '.config('ekraf.short_name').' untuk pertanyaan dan usulan kolaborasi.');
    }

    public function sitemap(): Response
    {
        $base = PublicSite::publicBase();
        abort_unless($base, 404);

        $paths = collect(['home', 'profile', 'maniis', 'subsectors', 'programs.index', 'activities.index', 'services', 'creative.hub', 'creative.market', 'creative.directory', 'creative.academy', 'creative.students', 'creative.data', 'creative.map', 'creative.help', 'creative.toolkit', 'creative.calendar', 'creative.space', 'contact'])
            ->map(fn (string $name) => route($name, absolute: false))
            ->concat(Program::recentlyPublished()->pluck('slug')->map(fn (string $slug) => route('programs.show', $slug, false)))
            ->concat(Activity::recentlyPublished()->pluck('slug')->map(fn (string $slug) => route('activities.show', $slug, false)));

        return response()->view('pages.sitemap', ['urls' => $paths->map(fn (string $path) => $base.$path)], 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    private function page(string $view, string $title, string $description, array $data = []): View
    {
        $site = config('ekraf');

        return view('pages.'.$view, $data + [
            'site' => $site,
            'pageTitle' => $title,
            'pageDescription' => $description,
            'canonical' => ($base = PublicSite::publicBase()) ? $base.request()->getPathInfo() : null,
            'logo' => PublicSite::image($site['assets']['logo']),
            'governmentLogo' => PublicSite::image($site['assets']['government_logo'], 'Logo Pemerintah Daerah'),
            'contacts' => PublicSite::contacts($site['contacts']),
        ]);
    }

    private function potentials(): array
    {
        return array_map(function (array $potential) {
            $potential['photo'] = $this->regionalPhoto($potential);

            return $potential;
        }, config('ekraf.potential'));
    }

    private function regionalPhoto(array $details): ?array
    {
        $location = $details['location'] ?? null;

        return filled($location)
            ? PublicSite::photo($details['image'] ?? null, $details['alt'] ?? null, $details['credit'] ?? null, $location)
            : null;
    }

    private function activityCovers(Collection $activities): Collection
    {
        return $activities->mapWithKeys(fn (Activity $activity) => [
            $activity->id => PublicSite::photo($activity->cover_path, $activity->cover_alt, $activity->cover_credit),
        ]);
    }

    private function galleryImages(Collection $photos): Collection
    {
        return $photos->mapWithKeys(fn (GalleryPhoto $photo) => [
            $photo->id => $this->galleryImage($photo),
        ])->filter();
    }

    private function galleryImage(GalleryPhoto $photo): ?array
    {
        return PublicSite::photo($photo->image_path, $photo->alt_text, $photo->credit);
    }
}
