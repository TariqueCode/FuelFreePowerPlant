<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::with('roles')->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => Role::with('permissions')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        $selectedRole = Role::findOrFail($data['role_id']);
        abort_if($selectedRole->slug === 'super-admin' && ! $request->user()->hasRole('super-admin'), 403, 'Only a Super Admin can assign the Super Admin role.');

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            ]);
            $user->roles()->sync([$data['role_id']]);
            return $user;
        });

        return redirect()->route('admin.users.index')->with('status', 'User account created successfully.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:12', 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        $selectedRole = Role::findOrFail($data['role_id']);
        if ($user->hasRole('super-admin') && ! $request->user()->hasRole('super-admin')) {
            abort(403, 'Only a Super Admin can modify a Super Admin account.');
        }
        if ($selectedRole->slug === 'super-admin' && ! $request->user()->hasRole('super-admin')) {
            abort(403, 'Only a Super Admin can assign the Super Admin role.');
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        DB::transaction(function () use ($user, $data) {
            $user->save();
            $user->roles()->sync([$data['role_id']]);
        });

        return redirect()->route('admin.users.index')->with('status', 'User account updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->hasRole('super-admin')) {
            abort_unless($request->user()->hasRole('super-admin'), 403, 'Only a Super Admin can delete a Super Admin account.');
        }

        abort_if($request->user()->is($user), 422, 'You cannot delete your own account.');

        if ($user->hasRole('super-admin')) {
            $remaining = User::where('id', '!=', $user->id)->get()
                ->filter(fn (User $candidate) => $candidate->hasRole('super-admin'))
                ->count();
            abort_if($remaining === 0, 422, 'The last super-admin account cannot be deleted.');
        }

        $user->delete();
        return back()->with('status', 'User account deleted successfully.');
    }
}
