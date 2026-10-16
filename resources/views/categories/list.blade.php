@extends('layouts.main')
@section('title', "Category: {$title}")

@section('header')
    <search>
        <form action="{{ route('categories.list') }}" method="get" class="app-cmp-search-form">
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
                        <a href="{{ route('categories.list') }}"
                            class="app-cl-button app-cl-warnning app-cl-filled">Clear</a>
                    </span>
                </div>
            </fieldset>
        </form>
    </search>

    @php
        session()->put('bookmarks.categories.create-form', url()->full());
        session()->put('bookmarks.categories.create', session()->get('bookmarks.categories.create-form'));
    @endphp

    <div class="app-cmp-links-bar">
        @can('create', \App\Models\Category::class)
            <a href="{{ route('categories.create-form') }}" class="app-cl-button app-cl-primary app-cl-filled">Create
                Category</a>
        @else
            <span></span>
        @endcan
        {{ $categories->withQueryString()->links() }}
    </div>
@endsection

@section('content')

    @php
        session()->put('bookmarks.categories.view', url()->full());
    @endphp

    <table class="app-cmp-data-list">
        <caption>List of Categories</caption>
        <colgroup>
            <col style="width: 0">
        </colgroup>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>No. of Products</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td>
                        <a href="{{ route('categories.view', ['category' => $category->code]) }}"
                            class="app-cl-link app-cl-code">{{ $category->code }}</a>
                    </td>
                    <td>{{ $category->name }}</td>
                    <td class="app-cl-number">{{ $category->products_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
