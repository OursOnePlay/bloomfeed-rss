<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Services\FluxCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SettingsController extends Controller
{
    private function ensureOwner(Request $request): void
    {
        abort_unless($request->user()->is_owner, 403);
    }

    public function index(Request $request): View
    {
        $this->ensureOwner($request);

        return view('settings', [
            'registrationsOpen' => Setting::registrationsOpen(),
            'users' => User::query()->orderBy('created_at')->get(),
            'catalogSources' => Setting::catalogSources(),
        ]);
    }

    public function addCatalogSource(Request $request, FluxCatalogService $catalog): RedirectResponse
    {
        $this->ensureOwner($request);

        $validated = $request->validate([
            'source_url' => ['required', 'url', 'max:2048'],
        ]);

        $sources = Setting::catalogSources();

        if (in_array($validated['source_url'], $sources, true)) {
            return redirect()->route('settings')->with('status', __('app.source_already_added'));
        }

        $count = $catalog->validateSource($validated['source_url']);

        if ($count === null) {
            return redirect()->route('settings')->withErrors(['source_url' => __('app.source_invalid')]);
        }

        $sources[] = $validated['source_url'];
        Setting::setCatalogSources($sources);

        return redirect()->route('settings')->with('status', __('app.source_added', ['count' => $count]));
    }

    public function removeCatalogSource(Request $request): RedirectResponse
    {
        $this->ensureOwner($request);

        $validated = $request->validate([
            'source_url' => ['required', 'string'],
        ]);

        Setting::setCatalogSources(array_filter(
            Setting::catalogSources(),
            fn (string $url) => $url !== $validated['source_url']
        ));

        return redirect()->route('settings')->with('status', __('app.source_removed'));
    }

    public function toggleRegistrations(Request $request): RedirectResponse
    {
        $this->ensureOwner($request);

        $open = ! Setting::registrationsOpen();
        Setting::set('registrations_open', $open ? '1' : '0');

        return redirect()->route('settings')
            ->with('status', $open ? __('app.registrations_now_open') : __('app.registrations_now_closed'));
    }

    public function createUser(Request $request): RedirectResponse
    {
        $this->ensureOwner($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('settings')->with('status', __('app.user_created'));
    }

    public function updateLocale(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'in:fr,en,es,de'],
        ]);

        session(['locale' => $validated['locale']]);

        if ($request->user()) {
            $request->user()->update(['locale' => $validated['locale']]);
        }

        return back();
    }
}
