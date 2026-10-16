@extends('layouts.main')
@section('title', "{$category->code}: Products")

@section('header')
    <search>
        <form action="{{ route('categories.view-products', ['category' => $category->code]) }}" method="get"
            class="app-cmp-search-form">
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
                        <a href="{{ route('categories.view-products', ['category' => $category->code]) }}"
                            class="app-cl-button app-cl-warnning app-cl-filled">Clear</a>
                    </span>
                </div>
            </fieldset>
        </form>
    </search>

    <div class="app-cmp-links-bar">
        <div class="app-cmp-form-actions">
            <a href="{{ session()->get('bookmarks.categories.view-products') ??
                route('categories.view', ['category' => $category->code]) }}"
                class="app-cl-button app-cl-primary">&lt; Back</a>
            @can('update', $category)
                <a href="{{ route('categories.add-products-form', ['category' => $category->code]) }}"
                    class="app-cl-button app-cl-primary app-cl-filled">Add Products</a>
            @endcan
        </div>
        {{ $products->withQueryString()->links() }}
    </div>
@endsection

@section('content')

    @php
        session()->put('bookmarks.products.view', url()->full());
        session()->put('bookmarks.categories.add-products-form', url()->full());
    @endphp

    <table class="app-cmp-data-list">
        <caption>List of Products for {{ $category->name }}</caption>
        <colgroup>
            <col style="width: 0">
        </colgroup>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Price</th>
                <th>No. of Shops</th>
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
                    <td class="app-cl-number">{{ number_format((float) $product->price, 2) }}</td>
                    <td class="app-cl-number">{{ $product->shops_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
