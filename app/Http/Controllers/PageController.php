<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('home', [
            'hasOwner' => User::query()->exists(),
        ]);
    }

    public function selfHosting(): View
    {
        return view('self-hosting');
    }
}
