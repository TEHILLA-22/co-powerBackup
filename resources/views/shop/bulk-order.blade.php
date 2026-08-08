{{-- resources/views/shop/bulk-order.blade.php --}}
@extends('layouts.app')

@section('title', 'Bulk Order Builder - Copower Wholesale')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="bulkOrderApp()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bulk Order Builder</h1>
            <p class="text-gray-600">Quickly add multiple products to your quote using SKU or EAN.</p>
        </div>
        <div class="mt-3 md:mt-0 flex space-x-3">
            <a href="{{ route('quote.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-file-invoice mr-2"></i>
                View Quote
                <span class="ml-2 bg-blue-400 text-white text-xs px-2 py-0.5 rounded-full">
                    {{ session('quote_count', 0) }}
                </span>
            </a>
        </div>
    </div>

    <!-- Paste from Clipboard / Spreadsheet -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Quick Paste</h2>
        <p class="text-sm text-gray-600 mb-3">Paste your order list (one per line): SKU/EAN, Quantity, Variant (optional)</p>
        <div class="flex flex-col sm:flex-row gap-3">
            <textarea x-model="pasteText" rows="4" class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                placeholder="Example:&#10;ABID04-E, 12, case&#10;5012616264320, 24, layer&#10;ABC123, 6"></textarea>
            <div class="flex flex-col space-y-2">
                <button @click="parsePaste()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-upload mr-2"></i> Parse & Add
                </button>
                <button @click="pasteText = ''" class="text-gray-500 text-sm hover:text-gray-700">Clear</button>
            </div>
        </div>
        <div x-show="pasteErrors.length > 0" class="mt-3 text-red-600 text-sm" x-html="pasteErrors.join('<br>')"></div>
    </div>

    <!-- Bulk Order Form -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Add Items Manually</h2>
            <button @click="addRow()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                <i class="fas fa-plus mr-1"></i> Add Row
            </button>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU / EAN</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Variant</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="bulkRows">
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-500" x-text="index + 1"></td>
                            <td class="px-4 py-3">
                                <input type="text" x-model="item.identifier"
                                    @input.debounce.500ms="validateRow(index)"
                                    placeholder="SKU or EAN"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </td>
                            <td class="px-4 py-3">
                                <div x-show="item.loading" class="text-sm text-gray-400">
                                    <i class="fas fa-spinner fa-spin"></i> Searching...
                                </div>
                                <div x-show="!item.loading && item.product_name" class="text-sm font-medium text-gray-800" x-text="item.product_name"></div>
                                <div x-show="!item.loading && !item.product_name && item.identifier" class="text-sm text-red-500">Not found</div>
                            </td>
                            <td class="px-4 py-3">
                                <select x-model="item.variant_type"
                                    @change="validateRow(index)"
                                    :disabled="!item.variants || item.variants.length === 0"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select</option>
                                    <template x-for="variant in (item.variants || [])" :key="variant.id">
                                        <option :value="variant.variant_type" x-text="variant.variant_type + ' (' + variant.quantity_per_unit + ' units)'"></option>
                                    </template>
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" x-model="item.quantity"
                                    @input="validateRow(index)"
                                    min="1"
                                    class="w-20 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                <span x-text="item.price ? '£' + Number(item.price).toFixed(2) : '-'"></span>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                <span x-text="item.total ? '£' + Number(item.total).toFixed(2) : '-'"></span>
                            </td>
                            <td class="px-4 py-3">
                                <button @click="removeRow(index)" class="text-red-500 hover:text-red-700 transition">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-right font-medium">
                            <span class="text-gray-600">Total Items:</span>
                            <span class="ml-2" x-text="items.length"></span>
                        </td>
                        <td colspan="2" class="px-4 py-4">
                            <button @click="submitBulk()"
                                :disabled="!hasValidItems() || isSubmitting"
                                class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isSubmitting"><i class="fas fa-cart-plus mr-2"></i> Add All to Quote</span>
                                <span x-show="isSubmitting"><i class="fas fa-spinner fa-spin mr-2"></i> Processing...</span>
                            </button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Validation Summary (Errors) -->
    <div x-show="validationErrors.length > 0" class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <h3 class="text-red-800 font-semibold">Validation Errors</h3>
        <ul class="list-disc list-inside text-sm text-red-600 mt-2">
            <template x-for="err in validationErrors" :key="err.index">
                <li x-text="'Row ' + (err.index + 1) + ': ' + err.error"></li>
            </template>
        </ul>
    </div>
</div>

@push('scripts')
<script>
function bulkOrderApp() {
    return {
        items: [],
        pasteText: '',
        pasteErrors: [],
        validationErrors: [],
        isSubmitting: false,

        init() {
            // Add one empty row
            this.addRow();
        },

        addRow() {
            this.items.push({
                identifier: '',
                quantity: 1,
                variant_type: null,
                product_name: null,
                product_id: null,
                variants: [],
                price: null,
                total: null,
                loading: false,
                validated: false,
                valid: false,
            });
        },

        removeRow(index) {
            this.items.splice(index, 1);
        },

        async validateRow(index) {
            const item = this.items[index];
            if (!item.identifier || item.identifier.length < 2) {
                return;
            }

            item.loading = true;
            item.valid = false;

            try {
                const response = await fetch('{{ route('quote.bulk-validate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        sku: item.identifier,
                        ean: item.identifier,
                        quantity: item.quantity,
                        variant_type: item.variant_type,
                    })
                });

                const data = await response.json();

                if (data.valid) {
                    item.product_name = data.product.name;
                    item.product_id = data.product.id;
                    item.variants = [data.product]; // we could expand this
                    item.price = data.pricing.unit_price;
                    item.total = data.pricing.total;
                    item.validated = true;
                    item.valid = true;
                    // Remove any previous error for this row
                    this.validationErrors = this.validationErrors.filter(e => e.index !== index);
                } else {
                    this.validationErrors.push({ index, error: data.error });
                    item.valid = false;
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.error) {
                    this.validationErrors.push({ index, error: error.response.data.error });
                } else {
                    this.validationErrors.push({ index, error: 'Validation failed' });
                }
                item.valid = false;
            } finally {
                item.loading = false;
            }
        },

        hasValidItems() {
            return this.items.some(item => item.valid === true);
        },

        async parsePaste() {
            if (!this.pasteText.trim()) return;

            try {
                const response = await fetch('{{ route('quote.parse-paste') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ text: this.pasteText })
                });

                const data = await response.json();

                if (data.items.length === 0) {
                    this.pasteErrors = ['No valid items found to parse. Ensure each line has SKU/EAN and Quantity.'];
                    return;
                }

                // Add parsed items to the table
                data.items.forEach(item => {
                    this.items.push({
                        identifier: item.sku || item.ean,
                        quantity: item.quantity || 1,
                        variant_type: item.variant_type || null,
                        product_name: null,
                        product_id: null,
                        variants: [],
                        price: null,
                        total: null,
                        loading: false,
                        validated: false,
                        valid: false,
                    });
                });

                // Validate each new row
                this.items.forEach((_, idx) => {
                    this.validateRow(idx);
                });

                this.pasteErrors = data.errors || [];
                this.pasteText = '';

            } catch (error) {
                this.pasteErrors = ['Failed to parse pasted text. Please check format.'];
            }
        },

        async submitBulk() {
            if (this.isSubmitting) return;

            const validItems = this.items.filter(item => item.valid === true);

            if (validItems.length === 0) {
                alert('No valid items to add to quote.');
                return;
            }

            this.isSubmitting = true;

            try {
                const payload = validItems.map(item => ({
                    sku: item.identifier,
                    quantity: item.quantity,
                    variant_type: item.variant_type,
                }));

                const response = await fetch('{{ route('quote.bulk-store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ items: payload })
                });

                const data = await response.json();

                if (data.success) {
                    // Clear the valid items from the table
                    this.items = this.items.filter(item => !item.valid);
                    // Refresh quote count in header via Livewire or page reload
                    window.location.href = '{{ route('quote.index') }}';
                } else {
                    alert(data.message || 'Failed to add items to quote.');
                }
            } catch (error) {
                alert('An error occurred while submitting the bulk order.');
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
</script>
@endpush
@endsection