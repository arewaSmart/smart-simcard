<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Display a listing of all system transactions.
     */
    public function index(Request $request): View
    {
        $query = Transaction::with('user');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_ref', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('performed_by', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Set pagination to 10 as requested
        $transactions = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Calculate global statistics
        $totalCredits = Transaction::whereIn('type', ['credit', 'refund', 'bonus', 'manual_credit'])
            ->where('status', 'completed')
            ->sum('amount');

        $totalDebits = Transaction::whereIn('type', ['debit', 'manual_debit'])
            ->where('status', 'completed')
            ->sum('amount');

        $totalCount = Transaction::count();

        return view('admin.manage.transaction', compact('transactions', 'totalCredits', 'totalDebits', 'totalCount'));
    }
}
