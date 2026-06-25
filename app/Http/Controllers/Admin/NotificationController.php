<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminBroadcastNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        Auth::user()->unreadNotifications->markAsRead();

        $notifications = Auth::user()
            ->notifications()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function readAll()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', __('app.notifications.all_marked_read'));
    }

    public function createBroadcast()
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);

        return view('admin.notifications.broadcast');
    }

    public function storeBroadcast(Request $request)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $admins = User::query()->where('role', User::ROLE_USER)->get();

        foreach ($admins as $admin) {
            $admin->notify(new AdminBroadcastNotification(
                $data['title'],
                $data['message'],
                Auth::user(),
            ));
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', __('app.notifications.sent', ['count' => $admins->count()]));
    }

    public static function summary(DatabaseNotification $notification): array
    {
        $data = $notification->data;

        if (($data['type'] ?? '') === 'form_created') {
            return [
                'icon' => 'form',
                'text' => __('app.notifications.form_created', [
                    'name' => $data['creator_name'] ?? '',
                    'role' => $data['creator_role'] ?? '',
                    'form' => $data['form_title'] ?? '',
                ]),
            ];
        }

        return [
            'icon' => 'broadcast',
            'text' => $data['title'] ?? __('app.notifications.broadcast'),
        ];
    }
}
