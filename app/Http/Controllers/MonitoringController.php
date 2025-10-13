<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function showLoginForm(): View
    {
        return view('monitoring.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('monitoring.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak valid.',
        ])->onlyInput('email');
    }

    public function dashboard(Request $request): View
    {
        $query = Asset::query()->with(['category', 'custodian'])->orderByDesc('updated_at');

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('department_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $status = $request->string('status')->trim()->lower()->toString();
        if ($status !== '') {
            $query->where('status', $status);
        }

        $assets = $query->paginate(20)->withQueryString();

        $totals = [
            'assets' => Asset::count(),
            'assigned' => Asset::where('status', 'assigned')->count(),
            'available' => Asset::where('status', 'available')->count(),
            'maintenance' => Asset::whereIn('status', ['maintenance', 'needs_check'])->count(),
        ];

        $statuses = Asset::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('monitoring.dashboard', [
            'pageTitle' => 'Monitoring Aset',
            'pageHeading' => 'Dashboard Monitoring Aset',
            'assets' => $assets,
            'totals' => $totals,
            'statuses' => $statuses,
            'filters' => [
                'search' => $request->query('search'),
                'status' => $status !== '' ? $status : null,
            ],
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('monitoring.login');
    }
}
