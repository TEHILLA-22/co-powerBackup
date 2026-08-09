{{-- resources/views/admin/products/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit ' . $product->name . ' - Copower Wholesale Admin')
@section('page_title', 'Edit Product: ' . $product->name)

@section('content')
@if($errors->any())
    <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 text-sm font-medium rounded-lg">
        @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-copower-dark mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">EAN *</label>
                        <input type="text" name="ean" value="{{ old('ean', $product->ean) }}" required maxlength="20" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">SKU *</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required maxlength="50" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Name *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required maxlength="255" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Brand</label>
                        <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Manufacturer</label>
                        <input type="text" name="manufacturer" value="{{ old('manufacturer', $product->manufacturer) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
                        <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">None</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                                @foreach($category->children ?? [] as $child)
                                    <option value="{{ $child->id }}" @selected(old('category_id', $product->category_id) == $child->id)>&nbsp;&nbsp;{{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Short Description</label>
                        <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
                        <textarea name="description" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- MOQ -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-copower-dark mb-4">Minimum Order Quantity</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">MOQ *</label>
                        <input type="number" name="moq" value="{{ old('moq', $product->moq) }}" min="1" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">MOQ Increment *</label>
                        <input type="number" name="moq_increment" value="{{ old('moq_increment', $product->moq_increment) }}" min="1" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="moq_enforced" value="1" @checked(old('moq_enforced', $product->moq_enforced)) class="rounded border-gray-300"> Enforce MOQ
                        </label>
                    </div>
                </div>
            </div>

            <!-- Variants -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-copower-dark mb-4">Pricing Variants</h3>
                <p class="text-xs text-gray-500 mb-4">Edit the buy-independent variants. Leave new rows to add a variant, blank price to remove.</p>
                @foreach($product->variants as $variant)
                    <div class="border border-gray-200 rounded-lg p-4 mb-3">
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                            <input type="hidden" name="variants[{{ $loop->index }}][id]" value="{{ $variant->id }}">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                                <select name="variants[{{ $loop->index }}][variant_type]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    @foreach(['unit','case','layer','pallet'] as $t)
                                        <option value="{{ $t }}" @selected($variant->variant_type == $t)>{{ ucfirst($t) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Qty/Unit</label>
                                <input type="number" name="variants[{{ $loop->index }}][quantity_per_unit]" value="{{ $variant->quantity_per_unit }}" min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Price *</label>
                                <input type="number" step="0.01" name="variants[{{ $loop->index }}][base_price]" value="{{ $variant->base_price }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Sale</label>
                                <input type="number" step="0.01" name="variants[{{ $loop->index }}][sale_price]" value="{{ $variant->sale_price }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Stock</label>
                                <input type="number" name="variants[{{ $loop->index }}][stock_quantity]" value="{{ $variant->stock_quantity }}" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Cost Price</label>
                                <input type="number" step="0.01" name="variants[{{ $loop->index }}][cost_price]" value="{{ $variant->cost_price }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Reorder Level</label>
                                <input type="number" name="variants[{{ $loop->index }}][reorder_level]" value="{{ $variant->reorder_level }}" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Weight (kg)</label>
                                <input type="number" step="0.001" name="variants[{{ $loop->index }}][weight_kg]" value="{{ $variant->weight_kg }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">MOQ</label>
                                <input type="number" name="variants[{{ $loop->index }}][moq]" value="{{ $variant->moq }}" min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-copower-dark mb-4">Media</h3>
                @if($product->main_image)
                    <img src="{{ asset('storage/' . $product->main_image) }}" alt="" class="h-24 w-24 object-cover rounded-lg mb-3">
                @endif
                <label class="block text-xs font-medium text-gray-500 mb-1">Main Image</label>
                <input type="file" name="main_image" accept="image/*" class="w-full text-sm mb-3">
                <label class="block text-xs font-medium text-gray-500 mb-1">Gallery Images</label>
                <input type="file" name="gallery_images[]" accept="image/*" multiple class="w-full text-sm">
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-copower-dark mb-4">Status</h3>
                <label class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active)) class="rounded border-gray-300"> Active
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured)) class="rounded border-gray-300"> Featured
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="is_on_sale" value="1" @checked(old('is_on_sale', $product->is_on_sale)) class="rounded border-gray-300"> On Sale
                </label>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <button type="submit" class="w-full bg-copower-banner text-white py-2.5 rounded-lg text-sm font-medium"><i class="fas fa-save mr-2"></i>Update Product</button>
                <a href="{{ route('admin.products.index') }}" class="block text-center mt-3 text-sm text-copower-banner hover:underline">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection