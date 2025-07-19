<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $incomeCategories = [
        'Salary',
        'Business Income',
        'Freelance/Side Hustles',
        'Investments',
        'Rental Income',
        'Dividends',
        'Interest Income',
        'Gifts',
        'Refunds/Reimbursements',
        'Bonuses'
    ];

    protected $expenseCategories = [
        'Housing',
        'Utilities',
        'Transportation',
        'Groceries',
        'Dining Out',
        'Healthcare',
        'Insurance',
        'Debt Payments',
        'Entertainment',
        'Personal Care'
    ];

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
            'source' => 'required|string',
            'category' => 'required|string',
            'price' => 'required|numeric',
            'currency'  => 'required|in:SYP,USD,EU,AED',
            'description' => 'nullable|string',
            'date' => 'required|date'
        ]);

        $type = $request->type_transaction;
        $categoryName = $request->category;

        $user = Auth::guard('user')->user();
        $company = Auth::guard('company')->user();


        $category = \App\Models\Category::firstOrCreate(
            [
                'name' => $categoryName,
                'type' => $type,
                'user_id' => $user ? $user->id : null
            ]
        );

        $transactionData = $request->only([
            'type_transaction',
            'source',
            'price',
            'currency',
            'description',
            'date'
        ]);


        $transactionData['category_id'] = $category->id;

        $transactionData['category'] = $categoryName;

        if ($user) {
            $transactionData['user_id'] = $user->id;
        } elseif ($company) {
            $transactionData['company_id'] = $company->id;
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $transaction = \App\Models\Transaction::create($transactionData);

        return response()->json($transaction, 201);
    }


    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'type_transaction' => 'required|in:income,expense',
    //         'source'           => 'required|string',
    //         'category'         => 'required|string',
    //         'price'            => 'required|numeric',
    //         'currency'         => 'required|in:SYP,USD,EU,AED',
    //         'description'      => 'nullable|string',
    //         'date'             => 'required|date'
    //     ]);

    //     $type = $request->type_transaction;
    //     $categoryName = $request->category;

    //     $user = Auth::guard('user')->user();
    //     $company = Auth::guard('company')->user();

    //     $category = Category::firstOrCreate(
    //         ['name' => $categoryName, 'type' => $type, 'user_id' => $user ? $user->id : null]
    //     );

    //     $transactionData = $request->only([
    //         'type_transaction',
    //         'source',
    //         'price',
    //         'currency',
    //         'description',
    //         'date'
    //     ]);

    //     $transactionData['category_id'] = $category->id;

    //     if ($user) {
    //         $transactionData['user_id'] = $user->id;
    //     } elseif ($company) {
    //         $transactionData['company_id'] = $company->id;
    //     } else {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }

    //     $transaction = Transaction::create($transactionData);

    //     return response()->json($transaction, 201);
    // }

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
    public function getCategories()
    {
        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $customIncomeCategories = Category::where('user_id', $user->id)
            ->where('type', 'income')
            ->pluck('name');

        $customExpenseCategories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->pluck('name');

        return response()->json([
            'income_categories' => $customIncomeCategories,
            'expense_categories' => $customExpenseCategories,
        ]);
    }
}
