<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin/manage_users', compact('users'));
    }

    public function deleteUser(User $user)
    {
        // Non permette di eliminare admin
        if ($user->is_admin) {
            return redirect()->route('admin.users')->with('error', 'Non puoi eliminare un admin.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Utente eliminato con successo.');
    }

    public function editUser(User $user)
    {
        return view('admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|max:255|unique:users,nickname,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('admin.users')->with('success', 'Utente aggiornato con successo.');
    }

}

