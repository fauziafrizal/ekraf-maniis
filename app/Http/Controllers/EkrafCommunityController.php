<?php

namespace App\Http\Controllers;

use App\Models\CreativeListing;
use App\Models\EkrafCredential;
use App\Models\EkrafFeedback;
use App\Models\EkrafPost;
use App\Models\EkrafReview;
use App\Models\Program;
use App\Support\EkrafMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EkrafCommunityController extends Controller
{
    const TYPES = ['forum' => 'Forum Komunitas', 'exhibition' => 'Pameran Karya', 'competition' => 'Kompetisi Kreativitas', 'challenge' => 'Creative Challenge', 'portfolio' => 'Portofolio Kreator', 'collaboration' => 'Peluang Kolaborasi'];

    public function index(Request $request)
    {
        $kind = $request->query('jenis', 'forum');
        abort_unless(is_string($kind) && isset(self::TYPES[$kind]), 404);

        return view('pages.community.index', ['pageTitle' => self::TYPES[$kind], 'types' => self::TYPES, 'kind' => $kind, 'posts' => EkrafPost::where('kind', $kind)->where('published', true)->latest()->paginate(18)->withQueryString()]);
    }

    public function submit(Request $request)
    {
        $data = $request->validate(['kind' => ['required', Rule::in(array_keys(self::TYPES))], 'title' => 'required|string|max:150', 'display_name' => 'required|string|max:100', 'body' => 'required|string|max:2000', 'url' => 'nullable|url:http,https|max:500', 'photo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4096', 'consent' => 'accepted']);
        unset($data['consent'],$data['photo']);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = EkrafMedia::saveImage($request->file('photo'));
        }
        EkrafPost::create($data + ['published' => false]);

        return back()->with('status', 'Kiriman diterima dan menunggu moderasi pengurus.');
    }

    public function photo(EkrafPost $post)
    {
        abort_unless($post->published && $post->photo_path && Storage::disk('local')->exists($post->photo_path), 404);

        return Storage::disk('local')->response($post->photo_path, null, ['Content-Type' => 'image/jpeg', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function listing(CreativeListing $listing)
    {
        abort_unless($listing->published, 404);
        $reviews = EkrafReview::where('creative_listing_id', $listing->id)->where('published', true)->latest()->paginate(20);
        $products = CreativeListing::public()->where('kind', 'product')->where('business_id', $listing->id)->get();
        $business = CreativeListing::public()->where('kind', 'business')->find($listing->business_id);

        return view('pages.community.listing', ['pageTitle' => $listing->name, 'listing' => $listing, 'reviews' => $reviews, 'products' => $products, 'business' => $business]);
    }

    public function listingPhoto(CreativeListing $listing)
    {
        abort_unless($listing->published && $listing->photo_path && Storage::disk('local')->exists($listing->photo_path), 404);

        return Storage::disk('local')->response($listing->photo_path, null, ['Content-Type' => 'image/jpeg', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function review(Request $request, CreativeListing $listing)
    {
        abort_unless($listing->published && $listing->kind === 'product', 404);
        $data = $request->validate(['display_name' => 'required|string|max:80', 'rating' => 'required|integer|min:1|max:5', 'body' => 'required|string|max:1000', 'consent' => 'accepted']);
        unset($data['consent']);
        EkrafReview::create($data + ['creative_listing_id' => $listing->id, 'published' => false]);

        return back()->with('status', 'Ulasan diterima dan menunggu moderasi. Ulasan bukan pembelian terverifikasi.');
    }

    public function feedbackForm()
    {
        return view('pages.community.feedback', ['pageTitle' => 'Survei Kepuasan']);
    }

    public function feedback(Request $request)
    {
        EkrafFeedback::create($request->validate(['rating' => 'required|integer|min:1|max:5', 'body' => 'nullable|string|max:1000']));

        return back()->with('status', 'Terima kasih. Masukan telah disimpan.');
    }

    public function verify(string $token)
    {
        $credential = EkrafCredential::where('token', $token)->where('active', true)->firstOrFail();

        return response()->view('pages.community.verify', ['pageTitle' => 'Verifikasi Dokumen', 'credential' => $credential])->header('X-Robots-Tag', 'noindex, nofollow')->header('Cache-Control', 'no-store');
    }

    public function map()
    {
        $businesses = CreativeListing::public()->where('kind', 'business')->whereNotNull('latitude')->whereNotNull('longitude')->orderBy('name')->get();

        return view('pages.community.map', ['pageTitle' => 'Peta UMKM & Tur Virtual', 'businesses' => $businesses]);
    }

    public function learning()
    {
        return view('pages.community.learning', ['pageTitle' => 'Creative Learning Challenge']);
    }

    public function alerts()
    {
        $programs = Program::published()->whereIn('status', ['akan_datang', 'berjalan'])->where('starts_at', '>=', now())->orderBy('starts_at')->limit(30)->get();

        return view('pages.community.alerts',['pageTitle' => 'Notifikasi Kegiatan', 'programs' => $programs]);
    }
}
