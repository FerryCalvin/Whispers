<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Post; // Import model Post jika belum diimport

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $posts = Post::all(); // Gantikan dengan query untuk mendapatkan postingan sesuai kebutuhan Anda
            return view('home', compact('user', 'posts'));
        } else {
            return redirect(route('login'));
        }
    }
}
