<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * List quotes pending review
     */
    public function index()
    {
        $quotes = Quote::with('user')
            ->where('status', 'submitted')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        $pendingCount = Quote::where('status', 'submitted')->count();

        return view('admin.quotes.index', compact('quotes', 'pendingCount'));
    }

    /**
     * Show quote details for review
     */
    public function show(Quote $quote)
    {
        $quote->load('user');

        return view('admin.quotes.show', compact('quote'));
    }

    /**
     * Approve a quote
     */
    public function approve(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $admin = auth()->guard('admin')->user();

        $quote->status = 'approved';
        $quote->approved_at = now();
        $quote->processed_by = $admin->id;
        $quote->review_notes = $validated['notes'] ?? null;
        $quote->save();

        AuditLog::log(
            'approve-quote',
            'quote',
            $quote->id,
            ['status' => 'submitted'],
            ['status' => 'approved'],
            "Quote {$quote->quote_number} approved by {$admin->full_name}"
        );

        return redirect()
            ->route('admin.quotes.index')
            ->with('success', "Quote {$quote->quote_number} approved.");
    }

    /**
     * Reject a quote
     */
    public function reject(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $admin = auth()->guard('admin')->user();

        $quote->status = 'rejected';
        $quote->processed_by = $admin->id;
        $quote->rejection_reason = $validated['rejection_reason'];
        $quote->save();

        AuditLog::log(
            'reject-quote',
            'quote',
            $quote->id,
            ['status' => 'submitted'],
            ['status' => 'rejected'],
            "Quote {$quote->quote_number} rejected by {$admin->full_name}"
        );

        return redirect()
            ->route('admin.quotes.index')
            ->with('warning', "Quote {$quote->quote_number} rejected.");
    }
}
