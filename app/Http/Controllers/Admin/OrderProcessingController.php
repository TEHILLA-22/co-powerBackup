<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\AuditLog;
use App\Mail\OrderApprovedMail;
use App\Mail\OrderRejectedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderProcessingController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * List orders pending processing
     */
    public function index()
    {
        $orders = Order::with(['user', 'items'])
            ->where('status', 'submitted')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        $pendingCount = Order::where('status', 'submitted')->count();
        $processingCount = Order::where('status', 'processing')->count();

        return view('admin.orders.processing', compact('orders', 'pendingCount', 'processingCount'));
    }

    /**
     * Show order details for processing
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items', 'items.product', 'items.variant']);

        return view('admin.orders.process', compact('order'));
    }

    /**
     * Start processing an order
     */
    public function startProcessing(Request $request, Order $order)
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $admin = auth()->guard('admin')->user();

        try {
            $order->startProcessing($admin->id, $validated['notes'] ?? null);

            AuditLog::log(
                'start-processing',
                'order',
                $order->id,
                ['status' => 'submitted'],
                ['status' => 'processing'],
                "Order {$order->order_number} processing started by " . $admin->full_name
            );

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', "Order {$order->order_number} is now being processed.");

        } catch (\Exception $e) {
            return back()
                ->withErrors(['processing' => $e->getMessage()]);
        }
    }

    /**
     * Approve order
     */
    public function approve(Request $request, Order $order)
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
            'verify_user' => ['nullable', 'boolean'],
        ]);

        $admin = auth()->guard('admin')->user();

        try {
            $order->approve(
                $admin->id,
                $validated['notes'] ?? null,
                $validated['verify_user'] ?? true
            );

            AuditLog::log(
                'approve-order',
                'order',
                $order->id,
                ['status' => 'processing'],
                ['status' => 'approved'],
                "Order {$order->order_number} approved by " . $admin->full_name
            );

            Mail::to($order->user->email)->queue(new OrderApprovedMail($order));

            return redirect()
                ->route('admin.orders.index')
                ->with('success', "Order {$order->order_number} approved successfully.");

        } catch (\Exception $e) {
            return back()
                ->withErrors(['approval' => $e->getMessage()]);
        }
    }

    /**
     * Reject order
     */
    public function reject(Request $request, Order $order)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $admin = auth()->guard('admin')->user();

        try {
            $order->reject($admin->id, $validated['rejection_reason']);

            AuditLog::log(
                'reject-order',
                'order',
                $order->id,
                ['status' => 'processing'],
                ['status' => 'rejected'],
                "Order {$order->order_number} rejected by " . $admin->full_name
            );

            Mail::to($order->user->email)->queue(new OrderRejectedMail($order, $validated['rejection_reason']));

            return redirect()
                ->route('admin.orders.index')
                ->with('warning', "Order {$order->order_number} rejected.");

        } catch (\Exception $e) {
            return back()
                ->withErrors(['rejection' => $e->getMessage()]);
        }
    }

    /**
     * Bulk approve orders
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'order_ids' => ['required', 'array'],
            'order_ids.*' => ['exists:orders,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $count = 0;
        $errors = [];

        foreach ($validated['order_ids'] as $orderId) {
            $order = Order::find($orderId);
            if ($order && in_array($order->status, ['submitted', 'processing'])) {
                try {
                    $order->approve(auth()->guard('admin')->id(), $validated['notes'] ?? null, true);
                    $count++;
                } catch (\Exception $e) {
                    $errors[] = $order->order_number . ': ' . $e->getMessage();
                }
            }
        }

        $message = "{$count} order(s) approved successfully.";
        if (!empty($errors)) {
            $message .= ' Errors: ' . implode('; ', $errors);
        }

        return redirect()
            ->route('admin.orders.index')
            ->with('success', $message);
    }
}
