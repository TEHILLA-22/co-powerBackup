{{-- resources/views/admin/settings/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Settings - Copower Wholesale Admin')
@section('page_title', 'Site Settings')

@section('content')
@if(session('success'))
    <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 text-sm font-medium rounded-lg">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 text-sm font-medium rounded-lg">
        @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @foreach($settings as $group => $groupSettings)
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
                    <h3 class="font-bold text-copower-dark mb-4 capitalize">{{ str_replace('_', ' ', $group) }}</h3>
                    <div class="space-y-4">
                        @foreach($groupSettings as $key => $value)
                            @php $setting = \App\Models\Setting::where('key', $key)->first(); @endphp
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ str_replace('_', ' ', ucfirst($key)) }}</label>
                                @if($setting && $setting->type === 'boolean')
                                    <select name="settings[{{ $key }}]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('settings.'.$key) border-red-500 @enderror">
                                        <option value="1" @selected((string) $value === '1')>Yes</option>
                                        <option value="0" @selected((string) $value !== '1')>No</option>
                                    </select>
                                @elseif($setting && $setting->type === 'array')
                                    <input type="text" name="settings[{{ $key }}]" value="{{ is_array($value) ? implode(',', $value) : $value }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <p class="text-xs text-gray-400 mt-1">Comma-separated</p>
                                @else
                                    <input type="text" name="settings[{{ $key }}]" value="{{ is_array($value) ? implode(',', $value) : $value }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                @endif
                                @if($setting && $setting->description)
                                    <p class="text-xs text-gray-400 mt-1">{{ $setting->description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <button type="submit" class="bg-copower-banner text-white px-6 py-2.5 rounded-lg text-sm font-medium"><i class="fas fa-save mr-2"></i>Save Settings</button>
        </form>
    </div>

    <!-- Add setting -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="font-bold text-copower-dark mb-4">Add Setting</h3>
            <form method="POST" action="{{ route('admin.settings.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Key</label>
                    <input type="text" name="key" required placeholder="my_setting" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Value</label>
                    <input type="text" name="value" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Group</label>
                    <input type="text" name="group" required placeholder="general" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="string">String</option>
                        <option value="integer">Integer</option>
                        <option value="boolean">Boolean</option>
                        <option value="array">Array</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
                    <input type="text" name="description" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <button type="submit" class="w-full bg-copower-dark text-white py-2 rounded-lg text-sm font-medium"><i class="fas fa-plus mr-2"></i>Add</button>
            </form>
        </div>

        @if($groups->count())
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-copower-dark mb-3">Groups</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($groups as $group)
                        <span class="px-2 py-1 bg-gray-100 rounded-full text-xs text-gray-600">{{ $group }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection