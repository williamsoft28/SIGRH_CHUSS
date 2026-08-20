<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuperAdminUserController extends Controller
{
    public function index()
    {
        $users = \App\Models\User::with('roles')->get();
        return view('super_admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::all();
        return view('super_admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'nullable|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('super_admin.users.index')->with('status', 'Utilisateur créé avec succès.');
    }
}
