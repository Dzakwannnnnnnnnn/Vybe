<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show($username)
    {
        // Cari user berdasarkan username, jika tidak ada tampilkan error 404
        $user = User::where('username', $username)->firstOrFail();

        // Ambil posts milik user tersebut beserta relasinya jika ada
        $posts = $user->posts()->latest()->get();

        // Kirim data ke view profile (sesuaikan dengan nama file blade kamu)
        return view('profile.show', compact('user', 'posts'));
    }
}