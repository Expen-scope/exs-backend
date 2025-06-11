<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $company = Auth::guard('company')->user();

        if (!$company) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($company->events, 200);
    }

    public function show($id)
    {
        $company = Auth::guard('company')->user();

        $event = Event::findOrFail($id);

        if ($event->company_id !== $company->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($event, 200);
    }

    public function store(Request $request)
    {
        $company = Auth::guard('company')->user();

        if (!$company) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'required|string',
            'scheduled_date' => 'required|date',
            'status'         => 'required|in:upcoming,in_progress,completed,cancelled,postponed',
        ]);

        $validated['company_id'] = $company->id;

        $event = Event::create($validated);

        return response()->json($event, 201);
    }

    public function update(Request $request, $id)
    {
        $company = Auth::guard('company')->user();
        $event = Event::findOrFail($id);

        if ($event->company_id !== $company->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'description'    => 'sometimes|string',
            'scheduled_date' => 'sometimes|date',
            'status'         => 'sometimes|in:upcoming,in_progress,completed,cancelled,postponed',
        ]);

        $event->update($validated);

        return response()->json($event, 200);
    }

    public function destroy($id)
    {
        $company = Auth::guard('company')->user();
        $event = Event::findOrFail($id);

        if ($event->company_id !== $company->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}
