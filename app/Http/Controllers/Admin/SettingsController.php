<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Show settings page
     */
    public function index()
    {
        $groups = Setting::select('group')->distinct()->pluck('group');
        $settings = [];

        foreach ($groups as $group) {
            $settings[$group] = SettingsService::getGroup($group);
        }

        return view('admin.settings.index', compact('settings', 'groups'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => ['required', 'array'],
        ]);

        DB::beginTransaction();

        try {
            foreach ($validated['settings'] as $key => $value) {
                // Validate based on type
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    // Cast value based on type
                    switch ($setting->type) {
                        case 'integer':
                            $value = (int) $value;
                            break;
                        case 'boolean':
                            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                            break;
                        case 'array':
                            $value = explode(',', $value);
                            break;
                    }

                    SettingsService::set($key, $value);
                }
            }

            DB::commit();

            // Clear all caches
            Cache::flush();

            return redirect()
                ->route('admin.settings.index')
                ->with('success', 'Settings updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Settings update failed: ' . $e->getMessage(), [
                'user' => auth()->id(),
                'settings' => $validated['settings'],
            ]);

            return back()
                ->withInput()
                ->withErrors(['update' => 'Failed to update settings: ' . $e->getMessage()]);
        }
    }

    /**
     * Add new setting
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:255', 'unique:settings'],
            'value' => ['nullable'],
            'group' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:string,integer,boolean,array'],
            'description' => ['nullable', 'string'],
            'is_public' => ['nullable', 'boolean'],
            'is_encrypted' => ['nullable', 'boolean'],
        ]);

        Setting::create([
            'key' => $validated['key'],
            'value' => $validated['value'],
            'group' => $validated['group'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'is_public' => $validated['is_public'] ?? false,
            'is_encrypted' => $validated['is_encrypted'] ?? false,
        ]);

        SettingsService::clearCache();

        return redirect()
            ->route('admin.settings.index')
            ->with('success', "Setting '{$validated['key']}' added successfully.");
    }

    /**
     * Delete setting
     */
    public function destroy($key)
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return back()->withErrors(['delete' => 'Setting not found.']);
        }

        $setting->delete();
        SettingsService::clearCache();

        return redirect()
            ->route('admin.settings.index')
            ->with('success', "Setting '{$key}' deleted successfully.");
    }
}
