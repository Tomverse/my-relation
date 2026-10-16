@extends('layouts.main')
@section('title', "Product: {$title}")
@section('content')

    <form action="{{ route('products.create') }}" method="post">
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
                <b>Category *</b>
                <select name="category" required>
                    <option value="" @selected(old('category') === null) disabled>--- Please Select Category ---</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->code }}" @selected($category->code === old('category'))>[{{ $category->code }}]
                            {{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <b>Price *</b>
                <input type="number" step="any" name="price" value="{{ old('price') }}" required
                    class="app-cl-number" />
            </label>
            <label>
                <b>Description *</b>
                <textarea name="description" cols="60" rows="10" required>{{ old('description') }}</textarea>
            </label>
        </div>
        <div class="app-cmp-form-actions">
            <button type="submit" class="app-cl-button app-cl-primary app-cl-filled">Create</button>
            <span class="app-cmp-form-secondary-actions">
                <a href="{{ session()->get('bookmarks.products.create-form') ?? route('products.index') }}"
                    class="app-cl-button app-cl-warnning app-cl-filled">Cancel</a>
            </span>
        </div>
    </form>

@endsection
