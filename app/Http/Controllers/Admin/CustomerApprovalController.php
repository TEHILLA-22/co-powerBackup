<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CustomerTier;
use App\Models\AuditLog;
use App\Mail\AccountApprovedMail;
use App\Mail\AccountRejectedMail;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class CustomerApprovalController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['admin'];
    }

    /**
     * List customers awaiting admin verification
     */
    public function index(Request $request)
    {
        $query = User::where('is_verified', true)
            ->where('is_admin_verified', false)
            ->with(['customerTier']);

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('company_name', 'LIKE', "%{$search}%")
                    ->orWhere('company_registration_number', 'LIKE', "%{$search}%");
            });
        }

        $pendingUsers = $query->orderBy('created_at', 'asc')->paginate(20);

        $pendingCount = User::where('is_verified', true)
            ->where('is_admin_verified', false)
            ->count();

        Cache::put('admin.pending_count', $pendingCount, 300);

        return view('admin.customers.pending', compact('pendingUsers', 'pendingCount'));
    }

    /**
     * Show a single customer
     */
    public function show(User $user)
    {
        $user->load(['addresses', 'customerTier', 'orders']);

        $tiers = CustomerTier::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('admin.customers.show', compact('user', 'tiers'));
    }

    /**
     * Verify a customer account
     */
    public function approve(Request $request, User $user)
    {
        $validated = $request->validate([
            'customer_tier_id' => ['required', 'exists:customer_tiers,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $admin = auth()->guard('admin')->user();

        try {
            $user->update([
                'is_admin_verified' => true,
                'admin_verified_at' => now(),
                'admin_verified_by' => $admin->id,
                'customer_tier_id' => $validated['customer_tier_id'],
            ]);

            AuditLog::log(
                'approve',
                'user',
                $user->id,
                ['is_admin_verified' => false],
                ['is_admin_verified' => true, 'tier' => $user->customerTier->name],
                'Customer verified by ' . $admin->full_name . ($validated['notes'] ? '. Notes: ' . $validated['notes'] : '')
            );

            Mail::to($user->email)->queue(new AccountApprovedMail($user));

            Cache::forget('admin.pending_count');

            return redirect()
                ->route('admin.customers.pending')
                ->with('success', "{$user->full_name} has been verified successfully.");
        } catch (\Exception $e) {
            \Log::error('Customer verification failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'admin_id' => $admin->id,
            ]);

            return back()
                ->withErrors(['approval' => 'Failed to verify customer. Please try again.']);
        }
    }

    /**
     * Deactivate a customer account
     */
    public function reject(Request $request, User $user)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $admin = auth()->guard('admin')->user();

        try {
            $user->unverifyAdmin($validated['rejection_reason']);

            AuditLog::log(
                'reject',
                'user',
                $user->id,
                ['is_active' => true],
                ['is_active' => false, 'reason' => $validated['rejection_reason']],
                'Customer deactivated by ' . $admin->full_name . '. Reason: ' . $validated['rejection_reason']
            );

            Mail::to($user->email)->queue(new AccountRejectedMail($user, $validated['rejection_reason']));

            Cache::forget('admin.pending_count');

            return redirect()
                ->route('admin.customers.pending')
                ->with('warning', "{$user->full_name} has been deactivated.");
        } catch (\Exception $e) {
            \Log::error('Customer deactivation failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'admin_id' => $admin->id,
            ]);

            return back()
                ->withErrors(['rejection' => 'Failed to deactivate customer. Please try again.']);
        }
    }

    /**
     * Bulk verify customers
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'customer_tier_id' => ['required', 'exists:customer_tiers,id'],
        ]);

        $admin = auth()->guard('admin')->user();

        $count = 0;
        $errors = [];

        foreach ($validated['user_ids'] as $userId) {
            $user = User::find($userId);
            if ($user && !$user->is_admin_verified) {
                try {
                    $user->update([
                        'is_admin_verified' => true,
                        'admin_verified_at' => now(),
                        'admin_verified_by' => $admin->id,
                        'customer_tier_id' => $validated['customer_tier_id'],
                    ]);

                    Mail::to($user->email)->queue(new AccountApprovedMail($user));

                    $count++;
                } catch (\Exception $e) {
                    $errors[] = $user->email . ': ' . $e->getMessage();
                }
            }
        }

        Cache::forget('admin.pending_count');

        $message = "{$count} customer(s) verified successfully.";
        if (!empty($errors)) {
            $message .= ' Errors: ' . implode('; ', $errors);
        }

        return redirect()
            ->route('admin.customers.pending')
            ->with($count > 0 ? 'success' : 'warning', $message);
    }
}