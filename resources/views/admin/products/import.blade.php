{{-- resources/views/admin/products/import.blade.php --}}
@extends('layouts.admin')

@section('title', 'Import Products - Copower Wholesale Admin')
@section('page_title', 'Import Products')

@section('content')
@if(session('success'))
    <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 text-sm font-medium rounded-lg">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 text-sm font-medium rounded-lg">
        @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- SIAN Price List -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-2 border-copower-banner">
        <h3 class="font-bold text-copower-dark mb-2">SIAN Supplier Price List</h3>
        <p class="text-sm text-gray-500 mb-4">Upload the raw SIAN price-list spreadsheet (Excel .xlsx layout: <code>No | EAN | Description | Med | Case Size | Layer | Pallet | Case GBP | GBP | 1.17 | 1.35 | Total Stock | To Order | Notes | MOQ</code>). Re-importing the same file updates existing products instead of duplicating them.</p>

        <form method="POST" action="{{ route('admin.products.import-sian') }}" enctype="multipart/form-data">
            @csrf
            <label class="block text-xs font-medium text-gray-500 mb-1" for="sian_file">SIAN Spreadsheet (.xlsx)</label>
            <input type="file" name="file" id="sian_file" accept=".xlsx,.xls" required class="w-full mb-3 text-sm">

            <button type="submit" class="w-full bg-copower-banner text-white py-2.5 rounded-lg text-sm font-medium"><i class="fas fa-file-import mr-2"></i>Import SIAN Price List</button>
            <p class="text-xs text-gray-400 mt-2">Creates a <strong>Unit</strong> variant and a <strong>Case</strong> variant per product from the Case Size column. Stock, notes and MOQ (order value) are preserved. Images are not included; add them later via each product's edit page.</p>
        </form>
    </div>

    <!-- Upload -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="font-bold text-copower-dark mb-2">Upload Spreadsheet</h3>
        <p class="text-sm text-gray-500 mb-4">Accepted formats: <strong>XLSX, XLS, CSV, TSV</strong>. Max 5MB.</p>

        <form method="POST" action="{{ route('admin.products.import') }}" enctype="multipart/form-data">
            @csrf
            <label class="block text-xs font-medium text-gray-500 mb-1" for="file">Spreadsheet File</label>
            <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv,.tsv" required class="w-full mb-3 text-sm">

            <label class="flex items-center gap-2 text-sm text-gray-600 mb-4">
                <input type="checkbox" name="skip_header" value="1" checked class="rounded border-gray-300">
                First row contains column headings (EAN, SKU, Name, ...)
            </label>

            <button type="submit" class="w-full bg-copower-banner text-white py-2.5 rounded-lg text-sm font-medium"><i class="fas fa-file-import mr-2"></i>Start Import</button>
        </form>
    </div>

    <!-- Template + Help -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="font-bold text-copower-dark mb-2">Download Template</h3>
            <p class="text-sm text-gray-500 mb-4">Use the template to match the expected columns exactly.</p>
            <div class="flex gap-2">
                <a href="{{ route('admin.products.template', ['format' => 'xlsx']) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium"><i class="fas fa-file-excel mr-2"></i>Excel (.xlsx)</a>
                <a href="{{ route('admin.products.template', ['format' => 'csv']) }}" class="border border-copower-dark text-copower-dark px-4 py-2 rounded-lg text-sm font-medium"><i class="fas fa-file-csv mr-2"></i>CSV</a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="font-bold text-copower-dark mb-3">Import Rules</h3>
            <ul class="space-y-2 text-sm text-gray-600 list-disc pl-4">
                <li>Match products by <strong>EAN</strong> or <strong>SKU</strong> to update; new combinations create new products.</li>
                <li>Columns: EAN, SKU, Name, Brand, Manufacturer, Category, Short Description, Description, MOQ, MOQ Enforced, MOQ Increment, Is Active, Is Featured, Is On Sale.</li>
                <li>Variant price columns: Unit Price / Unit Stock / Unit MOQ, Case Price / Case Stock / Case MOQ, Layer and Pallet alike.</li>
                <li>Leave a variant price blank to skip that variant.</li>
                <li>Booleans accept <code>Yes/No, 1/0, true/false</code>.</li>
            </ul>
        </div>

        <a href="{{ route('admin.products.index') }}" class="inline-block text-sm text-copower-banner hover:underline"><i class="fas fa-arrow-left mr-1"></i>Back to Products</a>
    </div>
</div>
@endsection