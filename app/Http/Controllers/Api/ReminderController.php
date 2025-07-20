<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reminders = Auth::user()->reminders()->orderBy('time', 'asc')->get();
        return response()->json($reminders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'collectedoprice' => 'required|numeric|min:0',
            'time' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $reminder = Auth::user()->reminders()->create($validator->validated());

        return response()->json($reminder, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($reminder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'collectedoprice' => 'sometimes|required|numeric|min:0',
            'time' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $reminder->update($validator->validated());

        return response()->json($reminder);
    }


    public function destroy(Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $reminder->delete();

        return response()->json(null, 204);
    }
}
