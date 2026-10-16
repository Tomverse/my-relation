@extends('layouts.main')
@section('title', "{$product->code}: Add Shops")

@section('header')
    <search>
        <form action="{{ route('products.add-shops-form', ['product' => $product->code]) }}" method="get"
            class="app-cmp-search-form">
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
                        <a href="{{ route('products.add-shops-form', ['product' => $product->code]) }}"
                            class="app-cl-button app-cl-warnning app-cl-filled">Clear</a>
                    </span>
                </div>
            </fieldset>
        </form>
    </search>

    <form action="{{ route('products.add-shop', ['product' => $product->code]) }}" id="app-form-add-shop" method="post">
        @csrf
    </form>

    <div class="app-cmp-links-bar">
        <a href="{{ session()->get('bookmarks.products.add-shops-form') ??
            route('products.view-shops', ['product' => $product->code]) }}"
            class="app-cl-button app-cl-primary">&lt; Back</a>
        {{ $shops->withQueryString()->links() }}
    </div>
@endsection

@section('content')

    @php
        session()->put('bookmarks.shops.view', url()->full());
    @endphp

    <table class="app-cmp-data-list">
        <caption>List of Shops for Adding to {{ $product->name }}</caption>
        <colgroup>
            <col style="width: 0">
        </colgroup>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Owner</th>
                <th>No. of Products</th>
                <th></th>
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
                    <td>
                        <button type="submit" form="app-form-add-shop" name="shop" value="{{ $shop->code }}"
                            class="app-cl-button app-cl-primary app-cl-filled">Add</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
