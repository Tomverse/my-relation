@extends('layouts.main')
@section('title', "{$shop->code}: Products")

@section('header')
    <search>
        <form action="{{ route('shops.view-products', ['shop' => $shop->code]) }}" method="get" class="app-cmp-search-form">
            <fieldset>
                <legend>Search</legend>
                <div class="app-cmp-data-form">
                    <label>
                        <b>Term</b>
                        <input type="text" name="term" value="{{ $criteria['term'] }}" />
                    </label>
                    <label>
                        <b>Min Price</b>
                        <input type="number" name="minPrice" value="{{ $criteria['minPrice'] }}" step="0.01"
                            class="app-cl-number" />
                    </label>
                    <label>
                        <b>Max Price</b>
                        <input type="number" name="maxPrice" value="{{ $criteria['maxPrice'] }}" step="0.01"
                            class="app-cl-number" />
                    </label>
                </div>
                <div class="app-cmp-form-actions">
                    <button type="submit" class="app-cl-button app-cl-primary app-cl-filled">Filter</button>
                    <span class="app-cmp-form-secondary-actions">
                        <a href="{{ route('shops.view-products', ['shop' => $shop->code]) }}"
                            class="app-cl-button app-cl-warnning app-cl-filled">Clear</a>
                    </span>
                </div>
            </fieldset>
        </form>
    </search>

    @can('update', $shop)
        <form action="{{ route('shops.remove-product', ['shop' => $shop->code]) }}" id="app-form-remove-product" method="post">
            @csrf
        </form>
    @endcan

    <div class="app-cmp-links-bar">
        <div class="app-cmp-form-actions">
            <a href="{{ session()->get('bookmarks.shops.view-products') ?? route('shops.view', ['shop' => $shop->code]) }}"
                class="app-cl-button app-cl-primary">&lt; Back</a>
            @can('update', $shop)
                <a href="{{ route('shops.add-products-form', ['shop' => $shop->code]) }}"
                    class="app-cl-button app-cl-primary app-cl-filled">Add Products</a>
            @endcan
        </div>
        {{ $products->withQueryString()->links() }}
    </div>
@endsection

@section('content')

    @php
        session()->put('bookmarks.products.view', url()->full());
        session()->put('bookmarks.shops.add-products-form', url()->full());
    @endphp

    <table class="app-cmp-data-list">
        <caption>List of Products for {{ $shop->name }}</caption>
        <colgroup>
            <col style="width: 0">
        </colgroup>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>No. of Shops</th>
                @can('update', $shop)
                    <th></th>
                @endcan
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>
                        <a href="{{ route('products.view', ['product' => $product->code]) }}"
                            class="app-cl-link app-cl-code">{{ $product->code }}</a>
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td class="app-cl-number">{{ number_format((float) $product->price, 2) }}</td>
                    <td class="app-cl-number">{{ $product->shops_count }}</td>
                    @can('update', $shop)
                        <td>
                            <button type="submit" form="app-form-remove-product" name="product" value="{{ $product->code }}"
                                class="app-cl-button app-cl-warnning app-cl-filled">Remove</button>
                        </td>
                    @endcan
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
