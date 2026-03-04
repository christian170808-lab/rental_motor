<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $admins = User::query()
            ->when($request->search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('admin.index', compact('admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => ['required', 'email', 'unique:users,email', 'regex:/@gmail\.com$/'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'email.regex' => 'Email must use @gmail.com domain.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.index')->with('success', 'Admin added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => ['required', 'email', 'unique:users,email,' . $id, 'regex:/@gmail\.com$/'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ], [
            'email.regex' => 'Email must use @gmail.com domain.',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        User::findOrFail($id)->update($data);

        return redirect()->route('admin.index')->with('success', 'Admin updated successfully!');
    }

    public function destroy($id)
    {
        $admin = User::findOrFail($id);

        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.index')->with('error', 'You cannot delete your own account.');
        }

        $admin->delete();

        return redirect()->route('admin.index')->with('success', 'Admin deleted successfully!');
    }
}