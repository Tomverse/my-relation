@extends('layouts.main')
@section('title', "Shop: {$title}")

@section('header')
@php
    session()->put('bookmarks.shops.view-products', url()->full());

    session()->put('bookmarks.shops.update-form', url()->full());
    session()->put(
        'bookmarks.shops.update',
        session()->get('bookmarks.shops.view'),
    );
    session()->put(
        'bookmarks.shops.delete',
        session()->get('bookmarks.shops.view'),
    );
@endphp

<nav>
@can('delete', $shop)
<form action="{{ route('shops.delete', ['shop' => $shop->code]) }}" method="post" id="app-form-delete">
@csrf
</form>
@endcan
<div class="app-cmp-form-actions">
<a href="{{
    session()->get('bookmarks.shops.view') ??
    route('shops.index')
}}" class="app-cl-button app-cl-primary">&lt; Back</a>
@can('view', $shop)
<a href="{{ route('shops.view-products', ['shop' => $shop->code]) }}" class="app-cl-button app-cl-primary">View Products</a>
@endcan
@can('update', $shop)
<a href="{{ route('shops.update-form', ['shop' => $shop->code]) }}" class="app-cl-button app-cl-primary app-cl-filled">Update</a>
@endcan
@can('delete', $shop)
<button type="submit" form="app-form-delete" class="app-cl-button app-cl-warnning app-cl-filled">Delete</button>
@endcan
</div>
</nav>
@endsection

@section('content')

<dl class="app-cmp-data-detail">
<dt>Code</dt>
<dd class="app-cl-code">{{ $shop->code }}</dd>

<dt>Name</dt>
<dd>{{ $shop->name }}</dd>

<dt>Owner</dt>
<dd>{{ $shop->owner }}</dd>

<dt>Location</dt>
<dd>
<span class="app-cl-number">{{ $shop->latitude }}</span>
<b>,</b>
<span class="app-cl-number">{{ $shop->longitude }}</span>
</dd>

<dt>Address</dt>
<dd>
<pre style="margin: 0;">{{ $shop->address }}</pre>
</dd>
</dl>

@endsection
