@extends('admin.layout')

@section('title', __('app.users.title'))

@section('content')
<div class="p-6 lg:p-10">
    <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-fc">{{ __('app.users.title') }}</h1>
            <p class="text-fc-muted text-sm mt-1">{{ __('app.users.subtitle') }}</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('app.users.create') }}
        </a>
    </div>

    @if($users->isEmpty())
    <div class="card p-14 text-center text-fc-muted">{{ __('app.users.empty') }}</div>
    @else
    <div class="table-wrap">
        <table class="w-full">
            <thead><tr>
                <th>{{ __('app.auth.name') }}</th>
                <th>{{ __('app.common.email') }}</th>
                <th>{{ __('app.users.forms_count') }}</th>
                <th>{{ __('app.common.created') }}</th>
                <th>{{ __('app.permissions.title') }}</th>
                <th class="text-right">{{ __('app.common.actions') }}</th>
            </tr></thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <span class="font-semibold text-fc">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="text-fc-muted">{{ $user->email }}</td>
                    <td>{{ $user->forms_count }}</td>
                    <td class="text-fc-muted">{{ $user->created_at->format('d.m.Y') }}</td>
                    <td>
                        @php $permCount = count($user->permissions ?? []); @endphp
                        @if($permCount > 0)
                        <span class="permission-pill">{{ __('app.permissions.count', ['count' => $permCount]) }}</span>
                        @else
                        <span class="text-xs text-fc-muted">{{ __('app.permissions.none') }}</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost text-xs py-1.5 text-violet-600">{{ __('app.common.edit') }}</a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm(@json(__('app.common.confirm_delete')))">@csrf @method('DELETE')
                            <button class="btn btn-danger text-xs py-1.5">{{ __('app.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $users->links() }}</div>
    @endif
</div>
@endsection
