@extends('layouts.main')
@section('title', "Category: {$title}")
@section('content')

    <form action="{{ route('categories.update', ['category' => $category->code]) }}" method="post">
        @csrf
        <div class="app-cmp-data-form">
            <label>
                <b>Code *</b>
                <input type="text" name="code" value="{{ old('code', $category->code) }}" required />
            </label>
            <label>
                <b>Name *</b>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required />
            </label>
            <label>
                <b>Description *</b>
                <textarea name="description" cols="60" rows="10" required>{{ old('description', $category->description) }}</textarea>
            </label>
        </div>
        <div class="app-cmp-form-actions">
            <button type="submit" class="app-cl-button app-cl-primary app-cl-filled">Update</button>
            <span class="app-cmp-form-secondary-actions">
                <a href="{{ session()->get('bookmarks.categories.update-form') ??
                    route('categories.view', ['category' => $category->code]) }}"
                    class="app-cl-button app-cl-warnning app-cl-filled">Cancel</a>
            </span>
        </div>
    </form>

@endsection
