<?php

namespace App\Http\Controllers;

use App\Models\CreativeListing;
use App\Support\EkrafMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreativeAdminController extends Controller
{
    public function loginForm()
    {
        if (Auth::check() && Auth::user()->is_ekraf_admin) {
            return redirect()->route('creative.admin');
        }

        return view('pages.hub.admin-login', ['pageTitle' => 'Login pengurus']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email|max:255', 'password' => 'required|string|max:255']);
        $accountKey = 'ekraf-login-account:'.hash('sha256', Str::lower($credentials['email']));
        $key = 'ekraf-login:'.hash('sha256', Str::lower($credentials['email']).'|'.$request->ip());
        if (RateLimiter::tooManyAttempts($key, 5) || RateLimiter::tooManyAttempts($accountKey, 12)) {
            return back()->withErrors(['email' => 'Terlalu banyak percobaan. Coba lagi nanti.'])->onlyInput('email');
        }
        if (! Auth::attempt($credentials + ['is_ekraf_admin' => true])) {
            RateLimiter::hit($key, 300);
            RateLimiter::hit($accountKey, 300);

            return back()->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])->onlyInput('email');
        }
        RateLimiter::clear($key);
        RateLimiter::clear($accountKey);
        $request->session()->regenerate();

        return redirect()->route('creative.admin');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(Auth::user()?->is_ekraf_admin, 403);
    }

    public function index()
    {
        $this->authorizeAdmin();

        return view('pages.hub.admin', ['pageTitle' => 'Panel pengurus', 'listings' => CreativeListing::latest()->paginate(25)]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'kind' => 'required|in:product,business', 'name' => 'required|string|max:150', 'category' => 'required|string|max:100',
            'village' => 'nullable|string|max:100', 'description' => 'required|string|max:3000',
            'price' => 'nullable|integer|min:0|max:100000000000', 'whatsapp' => 'nullable|regex:/^[1-9][0-9]{7,14}$/D',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4096',
            'latitude' => 'nullable|required_with:longitude|numeric|between:-90,90', 'longitude' => 'nullable|required_with:latitude|numeric|between:-180,180',
            'business_id' => ['nullable', 'integer', Rule::exists('creative_listings', 'id')->where('kind', 'business')],
            'published' => 'nullable|boolean', 'consent' => 'sometimes|accepted',
        ]);
        if ($request->boolean('published') && ! $request->boolean('consent')) {
            throw ValidationException::withMessages(['consent' => 'Konfirmasi izin publikasi sebelum menerbitkan data.']);
        }
        unset($data['consent'],$data['photo']);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = EkrafMedia::saveImage($request->file('photo'));
        }
        if ($data['kind'] === 'business') {
            $data['business_id'] = null;
        } else {
            $data['latitude'] = null;
            $data['longitude'] = null;
        }
        $data['published'] = $request->boolean('published');
        if ($data['kind'] === 'business') {
            $data['price'] = null;
        }

        return $data;
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        CreativeListing::create($this->validated($request));

        return back()->with('status', 'Data berhasil disimpan.');
    }

    public function update(Request $request, CreativeListing $listing)
    {
        $this->authorizeAdmin();
        $listing->update($this->validated($request));

        return back()->with('status', 'Data berhasil diperbarui.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
