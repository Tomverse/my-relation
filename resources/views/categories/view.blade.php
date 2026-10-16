@extends('layouts.main')
@section('title', "Category: {$title}")

@section('header')
@php
    session()->put('bookmarks.categories.view-products', url()->full());

   
    session()->put('bookmarks.categories.update-form', url()->full());
    session()->put(
        'bookmarks.categories.update',
        session()->get('bookmarks.categories.view'),
    );
    session()->put(
        'bookmarks.categories.delete',
        session()->get('bookmarks.categories.view'),
    );
@endphp

<nav>
@can('delete', $category)
<form action="{{ route('categories.delete', ['category' => $category->code]) }}" method="post" id="app-form-delete">
@csrf
</form>
@endcan
<div class="app-cmp-form-actions">
<a href="{{
    session()->get('bookmarks.categories.view') ??
    route('categories.index')
}}" class="app-cl-button app-cl-primary">&lt; Back</a>
@can('view', $category)
<a href="{{ route('categories.view-products', ['category' => $category->code]) }}" class="app-cl-button app-cl-primary">View Products</a>
@endcan
@can('update', $category)
<a href="{{ route('categories.update-form', ['category' => $category->code]) }}" class="app-cl-button app-cl-primary app-cl-filled">Update</a>
@endcan
@can('delete', $category)
<button type="submit" form="app-form-delete" class="app-cl-button app-cl-warnning app-cl-filled">Delete</button>
@endcan
</div>
</nav>
@endsection

@section('content')

<dl class="app-cmp-data-detail">
<dt>Code</dt>
<dd class="app-cl-code">{{ $category->code }}</dd>

<dt>Name</dt>
<dd>{{ $category->name }}</dd>
</dl>

<pre>{{ $category->description }}</pre>

@endsection
