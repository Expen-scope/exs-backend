<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $user = Auth::guard('user')->user();
        $company = Auth::guard('company')->user();

        if ($user) {
            return response()->json($user->goals);
        }

        if ($company) {
            return response()->json($company->goals);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string',
            'target_amount' => 'required|numeric',
            'saved_amount'  => 'required|numeric',
            'time'          => 'required|date'
        ]);

        $user = Auth::guard('user')->user();
        $company = Auth::guard('company')->user();

        $goalData = $request->only(['name', 'target_amount', 'saved_amount', 'time']);

        if ($user) {
            $goalData['user_id'] = $user->id;
        } elseif ($company) {
            $goalData['company_id'] = $company->id;
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $goal = Goal::create($goalData);

        return response()->json($goal, 201);
    }

    public function show($id)
    {
        $goal = Goal::findOrFail($id);

        if (
            (Auth::guard('user')->check() && $goal->user_id === Auth::id()) ||
            (Auth::guard('company')->check() && $goal->company_id === Auth::guard('company')->id())
        ) {
            return response()->json($goal);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function update(Request $request, $id)
    {
        $goal = Goal::findOrFail($id);

        if (
            (Auth::guard('user')->check() && $goal->user_id === Auth::id()) ||
            (Auth::guard('company')->check() && $goal->company_id === Auth::guard('company')->id())
        ) {
            $goal->update($request->only(['name', 'target_amount', 'saved_amount', 'time']));
            return response()->json($goal);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function destroy($id)
    {
        $goal = Goal::findOrFail($id);

        if (
            (Auth::guard('user')->check() && $goal->user_id === Auth::id()) ||
            (Auth::guard('company')->check() && $goal->company_id === Auth::guard('company')->id())
        ) {
            $goal->delete();
            return response()->json(['message' => 'Goal deleted']);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
