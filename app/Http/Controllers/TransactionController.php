<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $incomeCategories = ['راتب', 'أرباح', 'هدية', 'استثمار'];
    protected $expenseCategories = ['فاتورة كهرباء', 'فاتورة ماء', 'إيجار', 'وقود', 'طعام', 'انترنت'];

    public function index()
    {
        $user = Auth::guard('user')->user();
        $company = Auth::guard('company')->user();

        if ($user) {
            return response()->json($user->transactions);
        }

        if ($company) {
            return response()->json($company->transactions);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_transaction' => 'required|in:income,expense',
            'source'           => 'required|string',
            'category'         => 'required|string',
            'price'            => 'required|numeric',
            'currency'         => 'required|in:SYP,USD,EU,AED',
            'description'      => 'nullable|string',
            'date'             => 'required|date'
        ]);

        $type = $request->type_transaction;
        $category = $request->category;

        if ($type === 'income' && !in_array($category, $this->incomeCategories)) {
        }

        if ($type === 'expense' && !in_array($category, $this->expenseCategories)) {
        }

        $transactionData = $request->only([
            'type_transaction', 'source', 'category', 'price', 'currency', 'description', 'date'
        ]);

        $user = Auth::guard('user')->user();
        $company = Auth::guard('company')->user();

        if ($user) {
            $transactionData['user_id'] = $user->id;
        } elseif ($company) {
            $transactionData['company_id'] = $company->id;
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $transaction = Transaction::create($transactionData);

        return response()->json($transaction, 201);
    }

    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);

        $user = Auth::guard('user')->user();
        $company = Auth::guard('company')->user();

        if (
            ($user && $transaction->user_id === $user->id) ||
            ($company && $transaction->company_id === $company->id)
        ) {
            return response()->json($transaction);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $user = Auth::guard('user')->user();
        $company = Auth::guard('company')->user();

        if (
            ($user && $transaction->user_id === $user->id) ||
            ($company && $transaction->company_id === $company->id)
        ) {
            $request->validate([
                'type_transaction' => 'in:income,expense',
                'source'           => 'string',
                'category'         => 'string',
                'price'            => 'numeric',
                'currency'         => 'in:SYP,USD,EU,AED',
                'description'      => 'nullable|string',
                'date'             => 'date'
            ]);

            $transaction->update($request->all());

            return response()->json($transaction);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // ✅ حذف معاملة
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);

        $user = Auth::guard('user')->user();
        $company = Auth::guard('company')->user();

        if (
            ($user && $transaction->user_id === $user->id) ||
            ($company && $transaction->company_id === $company->id)
        ) {
            $transaction->delete();
            return response()->json(['message' => 'Transaction deleted']);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
