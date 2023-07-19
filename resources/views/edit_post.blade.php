@extends('layout')

@section('title', 'Edit Post')

@section('content')
<div class="container">
    <h1>Edit Postingan</h1>
    <form action="{{ route('post.update', $post->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <textarea class="form-control" name="content" rows="3" placeholder="Apa yang Anda pikirkan?">{{ $post->content }}</textarea>
        </div>
        <div class="form-group">
            <label for="media">Upload Media:</label>
            <input type="file" class="form-control" name="media[]" multiple>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
