<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->canViewAllForms()) {
            $forms = Form::with('user')
                ->withCount('responses')
                ->latest()
                ->take(5)
                ->get();

            $stats = [
                'total_forms' => Form::count(),
                'total_responses' => Form::withCount('responses')->get()->sum('responses_count'),
                'active_forms' => Form::where('is_active', true)->count(),
            ];

            if ($user->isSuperAdmin()) {
                $stats['total_users'] = User::where('role', User::ROLE_USER)->count();
            }
        } else {
            $forms = Form::where('user_id', $user->id)
                ->withCount('responses')
                ->latest()
                ->take(5)
                ->get();

            $stats = [
                'total_forms' => Form::where('user_id', $user->id)->count(),
                'total_responses' => Form::where('user_id', $user->id)
                    ->withCount('responses')
                    ->get()
                    ->sum('responses_count'),
                'active_forms' => Form::where('user_id', $user->id)->where('is_active', true)->count(),
            ];
        }

        return view('admin.dashboard', compact('forms', 'stats'));
    }
}
