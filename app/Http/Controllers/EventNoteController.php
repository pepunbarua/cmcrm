<?php

namespace App\Http\Controllers;

use App\Models\EventNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventNoteController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'note' => 'required|string'
        ]);

        $note = EventNote::create([
            'event_id' => $validated['event_id'],
            'user_id' => Auth::id(),
            'note' => $validated['note']
        ]);

        activity()
            ->performedOn($note)
            ->log('Added note to event');

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully',
            'note' => $note->load('user')
        ]);
    }

    public function destroy(EventNote $eventNote)
    {
        activity()
            ->performedOn($eventNote)
            ->log('Deleted event note');

        $eventNote->delete();

        return response()->json([
            'success' => true,
            'message' => 'Note deleted successfully'
        ]);
    }
}
