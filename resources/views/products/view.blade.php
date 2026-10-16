@extends('layouts.main')
@section('title', "Product: {$title}")

@section('header')
@php
    session()->put('bookmarks.products.view-shops', url()->full());

    session()->put(
        'bookmarks.products.delete',
        session()->get('bookmarks.products.view'),
    );
    session()->put('bookmarks.products.update-form', url()->full());
    session()->put(
        'bookmarks.products.update',
        session()->get('bookmarks.products.view'),
    );
@endphp

<nav>
@can('delete', $product)
<form action="{{ route('products.delete', ['product' => $product->code]) }}" method="post" id="app-form-delete">
@csrf
</form>
@endcan
<div class="app-cmp-form-actions">
<a href="{{
    session()->get('bookmarks.products.view') ??
    route('products.index')
}}" class="app-cl-button app-cl-primary">&lt; Back</a>
@can('view', $product)
<a href="{{ route('products.view-shops', ['product' => $product->code]) }}" class="app-cl-button app-cl-primary">View Shops</a>
@endcan
@can('update', $product)
<a href="{{ route('products.update-form', ['product' => $product->code]) }}" class="app-cl-button app-cl-primary app-cl-filled">Update</a>
@endcan
@can('delete', $product)
<button type="submit" form="app-form-delete" class="app-cl-button app-cl-warnning app-cl-filled">Delete</button>
@endcan
</div>
</nav>
@endsection

@section('content')

<dl class="app-cmp-data-detail">
<dt>Code</dt>
<dd class="app-cl-code">{{ $product->code }}</dd>

<dt>Name</dt>
<dd>{{ $product->name }}</dd>

<dt>Category</dt>
<dd>
<a href="{{ route('categories.view', ['category' => $product->category->code]) }}" class="app-cl-link">[{{ $product->category->code }}] {{ $product->category->name }}</a>
</dd>

<dt>Price</dt>
<dd>
<span class="app-cl-number" style="display: inline flow-root; width: 10ch;">{{ number_format((float)$product->price, 2) }}</span>
</dd>
</dl>

<pre>{{ $product->description }}</pre>

@endsection
