<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CustomerTier;
use App\Models\AuditLog;
use App\Mail\AccountApprovedMail;
use App\Mail\AccountRejectedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class CustomerApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * List pending registrations
     */
    public function index(Request $request)
    {
        $query = User::where('is_approved', false)
            ->whereNull('approved_at')
            ->with(['customerTier']);

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('company_name', 'LIKE', "%{$search}%")
                    ->orWhere('company_registration_number', 'LIKE', "%{$search}%");
            });
        }

        $pendingUsers = $query->orderBy('created_at', 'asc')
            ->paginate(20);

        // Get pending count for badge
        $pendingCount = User::where('is_approved', false)
            ->whereNull('approved_at')
            ->count();

        // Cache the pending count for 5 minutes
        Cache::put('admin.pending_count', $pendingCount, 300);

        return view('admin.customers.pending', compact('pendingUsers', 'pendingCount'));
    }

    /**
     * Show a single pending registration
     */
    public function show(User $user)
    {
        // Load relationships
        $user->load(['addresses', 'customerTier']);

        $tiers = CustomerTier::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('admin.customers.show', compact('user', 'tiers'));
    }

    /**
     * Approve a customer
     */
    public function approve(Request $request, User $user)
    {
        $validated = $request->validate([
            'customer_tier_id' => ['required', 'exists:customer_tiers,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $oldData = $user->toArray();

            // Update user
            $user->update([
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'customer_tier_id' => $validated['customer_tier_id'],
            ]);

            // Log approval
            AuditLog::log(
                'approve',
                'user',
                $user->id,
                ['is_approved' => false],
                ['is_approved' => true, 'tier' => $user->customerTier->name],
                'Customer approved by ' . auth()->user()->full_name . ($validated['notes'] ? '. Notes: ' . $validated['notes'] : '')
            );

            // Send approval email (queued)
            Mail::to($user->email)->queue(new AccountApprovedMail($user));

            // Clear pending count cache
            Cache::forget('admin.pending_count');

            return redirect()
                ->route('admin.customers.pending')
                ->with('success', "{$user->full_name} has been approved successfully.");

        } catch (\Exception $e) {
            \Log::error('Customer approval failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
            ]);

            return back()
                ->withErrors(['approval' => 'Failed to approve customer. Please try again.']);
        }
    }

    /**
     * Reject a customer
     */
    public function reject(Request $request, User $user)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        try {
            $oldData = $user->toArray();

            // Reject the user
            $user->update([
                'is_approved' => false,
                'rejection_reason' => $validated['rejection_reason'],
                'approved_by' => auth()->id(),
            ]);

            // Log rejection
            AuditLog::log(
                'reject',
                'user',
                $user->id,
                ['is_approved' => false],
                ['is_approved' => false, 'reason' => $validated['rejection_reason']],
                'Customer rejected by ' . auth()->user()->full_name . '. Reason: ' . $validated['rejection_reason']
            );

            // Send rejection email (queued)
            Mail::to($user->email)->queue(new AccountRejectedMail($user, $validated['rejection_reason']));

            // Clear pending count cache
            Cache::forget('admin.pending_count');

            return redirect()
                ->route('admin.customers.pending')
                ->with('warning', "{$user->full_name} has been rejected.");

        } catch (\Exception $e) {
            \Log::error('Customer rejection failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
            ]);

            return back()
                ->withErrors(['rejection' => 'Failed to reject customer. Please try again.']);
        }
    }

    /**
     * Bulk approve selected customers
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'customer_tier_id' => ['required', 'exists:customer_tiers,id'],
        ]);

        $count = 0;
        $errors = [];

        foreach ($validated['user_ids'] as $userId) {
            $user = User::find($userId);
            if ($user && !$user->is_approved) {
                try {
                    $user->update([
                        'is_approved' => true,
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                        'customer_tier_id' => $validated['customer_tier_id'],
                    ]);

                    // Send email
                    Mail::to($user->email)->queue(new AccountApprovedMail($user));

                    $count++;
                } catch (\Exception $e) {
                    $errors[] = $user->email . ': ' . $e->getMessage();
                }
            }
        }

        // Clear pending count cache
        Cache::forget('admin.pending_count');

        $message = "{$count} customer(s) approved successfully.";
        if (!empty($errors)) {
            $message .= ' Errors: ' . implode('; ', $errors);
        }

        return redirect()
            ->route('admin.customers.pending')
            ->with($count > 0 ? 'success' : 'warning', $message);
    }
}