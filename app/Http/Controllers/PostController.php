<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'media.*' => 'file|mimes:jpeg,png,mp4',
        ]);

        $post = new Post();
        $post->user_id = Auth::id();

        if ($request->has('content')) {
            $request->validate([
                'content' => 'required',
            ]);
            $post->content = $request->input('content');
        }

        $post->save();

        // Proses dan simpan media jika ada
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $filename = $file->store('media', 'public');

                // Resize gambar dengan lebar maksimum 800px
                if ($file->getClientOriginalExtension() === 'jpeg' || $file->getClientOriginalExtension() === 'png') {
                    $image = Image::make(public_path('storage/' . $filename));
                    $image->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->encode('jpg', 80)->save();
                }

                $media = new Media();
                $media->post_id = $post->id;
                $media->type = $file->getClientOriginalExtension();
                $media->filename = $filename;
                $media->save();
            }
        }

        $posts = Post::with('media')->latest()->get();

        return view('home', compact('posts'))->with("success", "Postingan berhasil ditambahkan!");
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);

        if (Auth::id() !== $post->user_id) {
            return redirect()->route('home')->with('error', 'Anda tidak memiliki izin untuk mengedit postingan ini.');
        }

        return view('edit_post', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required',
            'media.*' => 'file|mimes:jpeg,png,mp4',
        ]);

        $post = Post::findOrFail($id);

        if (Auth::id() !== $post->user_id) {
            return redirect()->route('home')->with('error', 'Anda tidak memiliki izin untuk mengedit postingan ini.');
        }

        $post->content = $request->input('content');
        $post->save();

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $filename = $file->store('media', 'public');

                // Resize gambar dengan lebar maksimum 800px
                if ($file->getClientOriginalExtension() === 'jpeg' || $file->getClientOriginalExtension() === 'png') {
                    $image = Image::make(public_path('storage/' . $filename));
                    $image->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->save();
                }

                $media = new Media();
                $media->post_id = $post->id;
                $media->type = $file->getClientOriginalExtension();
                $media->filename = $filename;
                $media->save();
            }
        }

        $posts = Post::with('media')->latest()->get();

        return view('home', compact('posts'))->with('success', 'Postingan berhasil diupdate.');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // Check if the authenticated user owns the post
        if (Auth::check() && Auth::id() === $post->user_id) {
            // Delete the associated media files
            foreach ($post->media as $media) {
                Storage::delete('public/' . $media->filename);
            }

            // Delete the post
            $post->delete();

            return redirect()->route('home')->with('success', 'Postingan berhasil dihapus.');
        } else {
            return redirect()->route('home')->with('error', 'Anda tidak memiliki izin untuk menghapus postingan ini.');
        }
    }
}
