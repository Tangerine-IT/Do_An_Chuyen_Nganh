<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->string('q')->trim()->value(), fn ($query, $term) => $query->where(fn ($query) => $query->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%")))
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->input('role')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users, 'roles' => UserRole::cases()]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['role' => ['required', 'in:customer,staff,admin']]);
        $user->update(['role' => $data['role']]);

        return back()->with('success', __('ui.admin.saved'));
    }

    public function toggleActive(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 422, __('ui.admin.cannot_deactivate_self'));
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', __('ui.admin.saved'));
    }
}
