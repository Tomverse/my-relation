@extends('layouts.main')
@section('title', "Shop: {$title}")

@section('header')
    <search>
        <form action="{{ route('shops.list') }}" method="get" class="app-cmp-search-form">
            <fieldset>
                <legend>Search</legend>
                <div class="app-cmp-data-form">
                    <label>
                        <b>Term</b>
                        <input type="text" name="term" value="{{ $criteria['term'] }}" />
                    </label>
                </div>
                <div class="app-cmp-form-actions">
                    <button type="submit" class="app-cl-button app-cl-primary app-cl-filled">Filter</button>
                    <span class="app-cmp-form-secondary-actions">
                        <a href="{{ route('shops.list') }}" class="app-cl-button app-cl-warnning app-cl-filled">Clear</a>
                    </span>
                </div>
            </fieldset>
        </form>
    </search>

    @php
        session()->put('bookmarks.shops.create-form', url()->full());
        session()->put('bookmarks.shops.create', session()->get('bookmarks.shops.create-form'));
    @endphp

    <div class="app-cmp-links-bar">
        @can('create', \App\Models\Shop::class)
            <a href="{{ route('shops.create-form') }}" class="app-cl-button app-cl-primary app-cl-filled">Create Shop</a>
        @else
            <span></span>
        @endcan
        {{ $shops->withQueryString()->links() }}
    </div>
@endsection

@section('content')

    @php
        session()->put('bookmarks.shops.view', url()->full());
    @endphp

    <table class="app-cmp-data-list">
        <caption>List of Shops</caption>
        <colgroup>
            <col style="width: 0">
        </colgroup>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Owner</th>
                <th>No. of Products</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shops as $shop)
                <tr>
                    <td>
                        <a href="{{ route('shops.view', ['shop' => $shop->code]) }}"
                            class="app-cl-link app-cl-code">{{ $shop->code }}</a>
                    </td>
                    <td>{{ $shop->name }}</td>
                    <td>{{ $shop->owner }}</td>
                    <td class="app-cl-number">{{ $shop->products_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
