<?php

namespace App\Http\Controllers;

use App\Models\Notifier;
use App\Services\NotifierDispatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NotifierController extends Controller
{
    public function index(Request $request): View
    {
        $notifiers = $request->user()->notifiers()->with('feeds')->orderBy('created_at')->get();
        $feeds = $request->user()->feeds()->orderBy('title')->get();

        return view('notifiers', [
            'notifiers' => $notifiers,
            'feeds' => $feeds,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        $notifier = $request->user()->notifiers()->create([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'url' => $validated['url'],
            'scope' => $validated['scope'],
        ]);

        if ($validated['scope'] === Notifier::SCOPE_FEEDS) {
            $notifier->feeds()->sync($validated['feed_ids'] ?? []);
        }

        return redirect()->route('notifiers')->with('status', __('app.notifier_created'));
    }

    public function update(Request $request, Notifier $notifier): RedirectResponse
    {
        $this->authorize('update', $notifier);

        $validated = $this->validatePayload($request);

        $notifier->update([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'url' => $validated['url'],
            'scope' => $validated['scope'],
        ]);

        $notifier->feeds()->sync($validated['scope'] === Notifier::SCOPE_FEEDS ? ($validated['feed_ids'] ?? []) : []);

        return redirect()->route('notifiers')->with('status', __('app.notifier_updated'));
    }

    public function toggle(Request $request, Notifier $notifier): RedirectResponse
    {
        $this->authorize('update', $notifier);

        $notifier->update(['enabled' => ! $notifier->enabled]);

        return redirect()->route('notifiers');
    }

    public function test(Notifier $notifier, NotifierDispatchService $dispatcher): RedirectResponse
    {
        $this->authorize('update', $notifier);

        $ok = $dispatcher->sendTest($notifier);

        return redirect()->route('notifiers')
            ->with('status', $ok ? __('app.notifier_test_ok') : __('app.notifier_test_failed'));
    }

    public function destroy(Notifier $notifier): RedirectResponse
    {
        $this->authorize('delete', $notifier);

        $notifier->delete();

        return redirect()->route('notifiers')->with('status', __('app.notifier_deleted'));
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'in:'.Notifier::TYPE_DISCORD.','.Notifier::TYPE_WEBHOOK],
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
            'scope' => ['required', 'in:'.Notifier::SCOPE_ALL.','.Notifier::SCOPE_FEEDS],
            'feed_ids' => ['array'],
            'feed_ids.*' => [
                'integer',
                Rule::exists('feeds', 'id')->where('user_id', $request->user()->id),
            ],
        ]);
    }
}
