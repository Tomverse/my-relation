@extends('layouts.main')
@section('title', "Shop: {$title}")
@section('content')

    <form action="{{ route('shops.update', ['shop' => $shop->code]) }}" method="post">
        @csrf
        <div class="app-cmp-data-form">
            <label>
                <b>Code *</b>
                <input type="text" name="code" value="{{ old('code', $shop->code) }}" required />
            </label>
            <label>
                <b>Name *</b>
                <input type="text" name="name" value="{{ old('name', $shop->name) }}" required />
            </label>
            <label>
                <b>Owner *</b>
                <input type="text" name="owner" value="{{ old('owner', $shop->owner) }}" required />
            </label>
            <label>
                <b>Latitude *</b>
                <input type="number" step="any" name="latitude" value="{{ old('latitude', $shop->latitude) }}" required
                    class="app-cl-number" />
            </label>
            <label>
                <b>Longitude *</b>
                <input type="number" step="any" name="longitude" value="{{ old('longitude', $shop->longitude) }}"
                    required class="app-cl-number" />
            </label>
            <label>
                <b>Address *</b>
                <textarea name="address" cols="60" rows="6" required>{{ old('address', $shop->address) }}</textarea>
            </label>
        </div>
        <div class="app-cmp-form-actions">
            <button type="submit" class="app-cl-button app-cl-primary app-cl-filled">Update</button>
            <span class="app-cmp-form-secondary-actions">
                <a href="{{ session()->get('bookmarks.shops.update-form') ?? route('shops.view', ['shop' => $shop->code]) }}"
                    class="app-cl-button app-cl-warnning app-cl-filled">Cancel</a>
            </span>
        </div>
    </form>

@endsection
