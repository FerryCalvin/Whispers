@extends('layout')

@section('title', 'Home Page')

@section('content')
<div class="container">
    <h1>Selamat Datang di Social Media Kita!</h1>
    <p>Temukan teman baru dan bagikan momen-momen berharga dalam hidup Anda.</p>

    <!-- Card Selamat Datang -->
    <div class="card my-4">
        <div class="card-header">
            @if (Auth::check())
                <h2>Selamat datang, {{ Auth::user()->name }}!</h2>
            @else
                <h2>Selamat datang, Pengunjung!</h2>
            @endif
        </div>
        <div class="card-body">
            <p>Selamat datang di Social Media Kita! Nikmati pengalaman berjejaring sosial yang menyenangkan dan interaktif.</p>
            <p>Bagikan momen berharga, berkomunikasi dengan teman-teman, dan temukan hal-hal menarik dari komunitas kami.</p>
        </div>
    </div>

    <!-- Card Posting Terbaru -->
    <div class="card mb-4">
        <div class="card-header">
            <h3>Posting Terbaru</h3>
        </div>
        <div class="card-body">
            <!-- Daftar postingan -->
            @foreach ($posts as $post)
                <div class="post">
                    <p class="post-author">{{ $post->user->name }}</p>
                    <p class="post-content">{{ $post->content }}</p>
                    <div class="post-options">
                        @if (Auth::check() && Auth::id() === $post->user_id)
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="postOptions{{ $post->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <!-- Three-dots icon -->
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="postOptions{{ $post->id }}">
                                    <!-- Edit Post -->
                                    <li><a class="dropdown-item" href="{{ route('post.edit', $post->id) }}">Edit</a></li>
                                    <!-- Delete Post -->
                                    <li>
                                        <form action="{{ route('post.destroy', $post->id) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>
                    @foreach ($post->media as $media)
                        @if ($media->type === 'jpeg' || $media->type === 'png')
                            <!-- Tampilkan gambar jika media berupa jpeg atau png -->
                            <img src="{{ asset('storage/' . $media->filename) }}" alt="Post Media" class="img-fluid mb-3 media-image">
                        @elseif ($media->type === 'mp4')
                            <!-- Tampilkan video jika media berupa mp4 -->
                            <video controls class="media-video">
                                <source src="{{ asset('storage/' . $media->filename) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @endif
                    @endforeach
                    <hr>
                </div>
            @endforeach
            <!-- Akhir daftar postingan -->
        </div>
    </div>

    <!-- Card Tulis Postingan Baru -->
    <div class="card">
        <div class="card-header">
            <h3>Tulis Postingan Baru</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('post.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <textarea class="form-control" name="content" rows="3" placeholder="Apa yang Anda pikirkan?"></textarea>
                </div>
                <div class="form-group">
                    <label for="media">Upload Media:</label>
                    <input type="file" class="form-control" name="media[]" multiple>
                </div>
                <button type="submit" class="btn btn-primary">Bagikan</button>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="css/style.css">

@endsection
    