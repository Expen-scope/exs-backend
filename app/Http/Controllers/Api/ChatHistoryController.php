<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatHistory;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ChatHistoryController extends Controller
{

     public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'     => 'required|integer|exists:users,id',
            'messages'    => 'required|array|min:1',
            'messages.*.role' => 'required|in:user,assistant',
            'messages.*.content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $user_id = $validatedData['user_id'];

        $modelClass = User::class;

        try {
            DB::transaction(function () use ($modelClass, $user_id, $validatedData) {
                foreach ($validatedData['messages'] as $message) {
                    ChatHistory::create([

                        'chattable_type' => $modelClass,
                        'chattable_id'   => $user_id,
                        'role'           => $message['role'],
                        'content'        => $message['content'],
                    ]);
                }
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save user chat history.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json(['message' => 'User chat history saved successfully.'], 201);
    }
}
