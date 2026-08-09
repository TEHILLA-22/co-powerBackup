{{-- resources/views/admin/products/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Add Product - Copower Wholesale Admin')
@section('page_title', 'Add Product')

@section('content')
@if($errors->any())
    <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 text-sm font-medium rounded-lg">
        @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Basic Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-copower-dark mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">EAN *</label>
                        <input type="text" name="ean" value="{{ old('ean') }}" required maxlength="20" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">SKU *</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" required maxlength="50" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required maxlength="255" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Brand</label>
                        <input type="text" name="brand" value="{{ old('brand') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Manufacturer</label>
                        <input type="text" name="manufacturer" value="{{ old('manufacturer') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
                        <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">None</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @foreach($category->children ?? [] as $child)
                                    <option value="{{ $child->id }}">&nbsp;&nbsp;{{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Short Description</label>
                        <input type="text" name="short_description" value="{{ old('short_description') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
                        <textarea name="description" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- MOQ -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-copower-dark mb-4">Minimum Order Quantity</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">MOQ *</label>
                        <input type="number" name="moq" value="{{ old('moq', 1) }}" min="1" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">MOQ Increment *</label>
                        <input type="number" name="moq_increment" value="{{ old('moq_increment', 1) }}" min="1" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="moq_enforced" value="1" checked class="rounded border-gray-300"> Enforce MOQ
                        </label>
                    </div>
                </div>
            </div>

            <!-- Variants -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-copower-dark mb-4">Pricing Variants</h3>
                <p class="text-xs text-gray-500 mb-4">Add the unit, case, layer and pallet options for this product.</p>
                @php
                    $variantDefs = [
                        ['type' => 'unit', 'name' => 'Unit'],
                        ['type' => 'case', 'name' => 'Case'],
                        ['type' => 'layer', 'name' => 'Layer'],
                        ['type' => 'pallet', 'name' => 'Pallet'],
                    ];
                @endphp
                @foreach($variantDefs as $def)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 variant-block" data-type="{{ $def['type'] }}">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-copower-dark text-sm">{{ $def['name'] }}</h4>
                            <label class="flex items-center gap-2 text-xs text-gray-500">
                                <input type="checkbox" class="variant-toggle rounded border-gray-300" checked> Enabled
                            </label>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 variant-fields">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Quantity / Unit</label>
                                <input type="number" name="variants[{{ $loop->index }}][quantity_per_unit]" value="{{ old('variants.'.$loop->index.'.quantity_per_unit', 1) }}" min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" data-req>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Base Price *</label>
                                <input type="number" step="0.01" name="variants[{{ $loop->index }}][base_price]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Sale Price</label>
                                <input type="number" step="0.01" name="variants[{{ $loop->index }}][sale_price]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Stock</label>
                                <input type="number" name="variants[{{ $loop->index }}][stock_quantity]" value="0" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <input type="hidden" name="variants[{{ $loop->index }}][variant_type]" value="{{ $def['type'] }}">
                            <input type="hidden" name="variants[{{ $loop->index }}][variant_name]" value="{{ $def['name'] }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-copower-dark mb-4">Media</h3>
                <label class="block text-xs font-medium text-gray-500 mb-1">Main Image</label>
                <input type="file" name="main_image" accept="image/*" class="w-full text-sm mb-3">
                <label class="block text-xs font-medium text-gray-500 mb-1">Gallery Images</label>
                <input type="file" name="gallery_images[]" accept="image/*" multiple class="w-full text-sm">
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-copower-dark mb-4">Status</h3>
                <label class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300"> Active
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                    <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300"> Featured
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="is_on_sale" value="1" class="rounded border-gray-300"> On Sale
                </label>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <button type="submit" class="w-full bg-copower-banner text-white py-2.5 rounded-lg text-sm font-medium"><i class="fas fa-save mr-2"></i>Save Product</button>
                <a href="{{ route('admin.products.index') }}" class="block text-center mt-3 text-sm text-copower-banner hover:underline">Cancel</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.querySelectorAll('.variant-toggle').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            var block = this.closest('.mb-4');
            var fields = block.querySelectorAll('.variant-fields input:not([type=hidden])');
            fields.forEach(function (input) {
                input.disabled = !toggle.checked;
                if (!toggle.checked) input.removeAttribute('required');
            });
        });
    });
</script>
@endpush
@endsection