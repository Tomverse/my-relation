@extends('layouts.main')
@section('title', "Category: {$title}")
@section('content')

    <form action="{{ route('categories.create') }}" method="post">
        @csrf
        <div class="app-cmp-data-form">
            <label>
                <b>Code *</b>
                <input type="text" name="code" value="{{ old('code') }}" required />
            </label>
            <label>
                <b>Name *</b>
                <input type="text" name="name" value="{{ old('name') }}" required />
            </label>
            <label>
                <b>Description *</b>
                <textarea name="description" cols="60" rows="10" required>{{ old('description') }}</textarea>
            </label>
        </div>
        <div class="app-cmp-form-actions">
            <button type="submit" class="app-cl-button app-cl-primary app-cl-filled">Create</button>
            <span class="app-cmp-form-secondary-actions">
                <a href="{{ session()->get('bookmarks.categories.create-form') ?? route('categories.index') }}"
                    class="app-cl-button app-cl-warnning app-cl-filled">Cancel</a>
            </span>
        </div>
    </form>

@endsection
