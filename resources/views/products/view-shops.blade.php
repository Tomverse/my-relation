@extends('layouts.main')
@section('title', "{$product->code}: Shops")

@section('header')
    <search>
        <form action="{{ route('products.view-shops', ['product' => $product->code]) }}" method="get"
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
                        <a href="{{ route('products.view-shops', ['product' => $product->code]) }}"
                            class="app-cl-button app-cl-warnning app-cl-filled">Clear</a>
                    </span>
                </div>
            </fieldset>
        </form>
    </search>

    @can('update', $product)
        <form action="{{ route('products.remove-shop', ['product' => $product->code]) }}" id="app-form-remove-shop"
            method="post">
            @csrf
        </form>
    @endcan

    <div class="app-cmp-links-bar">
        <div class="app-cmp-form-actions">
            <a href="{{ session()->get('bookmarks.products.view-shops') ?? route('products.view', ['product' => $product->code]) }}"
                class="app-cl-button app-cl-primary">&lt; Back</a>
            @can('update', $product)
                <a href="{{ route('products.add-shops-form', ['product' => $product->code]) }}"
                    class="app-cl-button app-cl-primary app-cl-filled">Add Shops</a>
            @endcan
        </div>
        {{ $shops->withQueryString()->links() }}
    </div>
@endsection

@section('content')

    @php
        session()->put('bookmarks.shops.view', url()->full());
        session()->put('bookmarks.products.add-shops-form', url()->full());
    @endphp

    <table class="app-cmp-data-list">
        <caption>List of Shops for {{ $product->name }}</caption>
        <colgroup>
            <col style="width: 0">
        </colgroup>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Owner</th>
                <th>No. of Products</th>
                @can('update', $product)
                    <th></th>
                @endcan
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
                    @can('update', $product)
                        <td>
                            <button type="submit" form="app-form-remove-shop" name="shop" value="{{ $shop->code }}"
                                class="app-cl-button app-cl-warnning app-cl-filled">Remove</button>
                        </td>
                    @endcan
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
