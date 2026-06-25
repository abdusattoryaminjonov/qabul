<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\UserPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->where('role', User::ROLE_USER)
            ->withCount('forms')
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $permissionLabels = UserPermissions::labels();
        $permissionDescriptions = UserPermissions::descriptions();

        return view('admin.users.create', compact('permissionLabels', 'permissionDescriptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(UserPermissions::keys())],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_USER,
            'permissions' => array_values($data['permissions'] ?? []),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', __('app.users.created'));
    }

    public function edit(User $user)
    {
        abort_if($user->isSuperAdmin(), 404);

        $permissionLabels = UserPermissions::labels();
        $permissionDescriptions = UserPermissions::descriptions();

        return view('admin.users.edit', compact('user', 'permissionLabels', 'permissionDescriptions'));
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->isSuperAdmin(), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(UserPermissions::keys())],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->permissions = array_values($data['permissions'] ?? []);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', __('app.users.updated'));
    }

    public function destroy(User $user)
    {
        abort_if($user->isSuperAdmin(), 403);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', __('app.users.deleted'));
    }
}
