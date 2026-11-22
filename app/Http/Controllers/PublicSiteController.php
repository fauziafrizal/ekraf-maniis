<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\BoardMember;
use App\Models\GalleryPhoto;
use App\Models\Program;
use App\Models\Subsector;
use App\Support\PublicSite;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PublicSiteController extends Controller
{
    public function home(): View
    {
        $site = config('ekraf');
        $hero = $site['assets']['hero'];
        $heroImage = PublicSite::image($hero['image'], $hero['alt'], $hero['credit'], $hero['location']);
        $activities = Activity::recentlyPublished()->limit(3)->get();

        return $this->page('home', 'Beranda', $site['intro'], [
            'heroImage' => $heroImage,
            'potentials' => $this->potentials(),
            'programs' => Program::recentlyPublished()->limit(3)->get(),
            'activities' => $activities,
            'activityCovers' => $this->activityCovers($activities),
            'metaImage' => $heroImage,
        ]);
    }

    public function profile(): View
    {
        $members = BoardMember::published()->inDisplayOrder()->get();
        $chair = $members->first(fn (BoardMember $member) => mb_strtolower(trim($member->position)) === 'ketua');

        return $this->page('profile', 'Profil', 'Kenali organisasi dan struktur pengurus '.config('ekraf.short_name').' periode '.config('ekraf.period').'.', [
            'members' => $members,
            'chair' => $chair,
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

    public function contact(): View
    {
        return $this->page('contact', 'Kontak', 'Temukan kanal resmi pengurus '.config('ekraf.short_name').' untuk pertanyaan dan usulan kolaborasi.');
    }

    public function sitemap(): Response
    {
        $base = PublicSite::publicBase();
        abort_unless($base, 404);

        $paths = collect(['home', 'profile', 'maniis', 'subsectors', 'programs.index', 'activities.index', 'contact'])
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
            $potential['photo'] = filled($potential['location'])
                ? PublicSite::photo($potential['image'], $potential['alt'], $potential['credit'], $potential['location'])
                : null;

            return $potential;
        }, config('ekraf.potential'));
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
            $photo->id => PublicSite::photo($photo->image_path, $photo->alt_text, $photo->credit),
        ])->filter();
    }
}
