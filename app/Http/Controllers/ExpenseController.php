<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::where('company_id', $request->user()->company_id)->latest('date')->get();
        return response()->json(['data' => $expenses]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data['company_id'] = $request->user()->company_id;

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $expense = Expense::create($data);

        return response()->json(['data' => $expense], 201);
    }

    public function show(Request $request, Expense $expense)
    {
        abort_unless($expense->company_id === $request->user()->company_id, 403);
        return response()->json(['data' => $expense]);
    }

    public function update(Request $request, Expense $expense)
    {
        abort_unless($expense->company_id === $request->user()->company_id, 403);

        $data = $request->validate([
            'category' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'date' => 'sometimes|required|date',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $expense->update($data);

        return response()->json(['data' => $expense]);
    }

    public function destroy(Request $request, Expense $expense)
    {
        abort_unless($expense->company_id === $request->user()->company_id, 403);
        $expense->delete();
        return response()->json(['message' => 'Expense deleted'], 200);
    }
}
