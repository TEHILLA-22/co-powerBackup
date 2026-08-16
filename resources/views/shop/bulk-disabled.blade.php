@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-12 p-6 bg-white rounded-lg shadow">
    <div class="text-center">
        <img src="{{ asset('images/copower-logo.png') }}" alt="Copower Wholesale" style="max-height:64px;margin:0 auto 16px;">
        <h1 class="text-2xl font-semibold text-gray-800">Bulk Order Builder Disabled</h1>
        <p class="mt-2 text-gray-600">The bulk order/quote upload feature is currently disabled by an administrator.</p>
        <div class="mt-4">
            <a href="{{ route('quote.index') }}" class="inline-block px-4 py-2 bg-copower-dark text-white rounded">Back to Quote</a>
            <a href="{{ route('customer.products') }}" class="inline-block ml-2 px-4 py-2 border border-gray-200 rounded">Browse Products</a>
        </div>
        <p class="mt-4 text-sm text-gray-500">If you believe this is an error, please contact support at <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.</p>
    </div>
</div>
@endsection
