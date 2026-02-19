<?php

namespace App\Http\Controllers;

use App\Models\Deliverable;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DeliverableController extends Controller
{
    public function index(Request $request)
    {
        $query = Deliverable::with(['event.order.customer', 'event.order.lead']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by event
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // Search by client name or file name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                  ->orWhereHas('event.order', function($q2) use ($search) {
                      $q2->where('client_name', 'like', "%{$search}%")
                          ->orWhereHas('customer', function ($customerQuery) use ($search) {
                              $customerQuery->where('full_name', 'like', "%{$search}%")
                                  ->orWhere('phone', 'like', "%{$search}%");
                          })
                          ->orWhereHas('lead', function ($leadQuery) use ($search) {
                              $leadQuery->where('client_name', 'like', "%{$search}%")
                                  ->orWhere('client_phone', 'like', "%{$search}%");
                          });
                  });
            });
        }

        $deliverables = $query->orderBy('created_at', 'desc')->paginate(12);

        // Statistics
        $stats = [
            'total' => Deliverable::count(),
            'pending' => Deliverable::where('status', 'pending')->count(),
            'uploaded' => Deliverable::where('status', 'uploaded')->count(),
            'delivered' => Deliverable::where('status', 'delivered')->count(),
        ];

        $events = Event::with(['order.customer', 'order.lead'])->orderBy('event_date', 'desc')->get();

        return view('deliverables.index', compact('deliverables', 'stats', 'events'));
    }

    public function create(Request $request)
    {
        $events = Event::with(['order.customer', 'order.lead'])
                      ->whereIn('status', ['completed', 'in_progress'])
                      ->orderBy('event_date', 'desc')
                      ->get();

        $selectedEventId = $request->get('event_id');

        return view('deliverables.create', compact('events', 'selectedEventId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'file_type' => 'required|in:photo,video,album,highlights',
            'file' => 'required|file|max:512000', // 500MB max
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:10',
        ]);

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            // Generate unique file name
            $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;
            
            // Store file
            $filePath = $file->storeAs('deliverables', $fileName, 'public');
            
            // Create deliverable record
            $deliverable = Deliverable::create([
                'event_id' => $validated['event_id'],
                'file_type' => $validated['file_type'],
                'file_name' => $originalName,
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'description' => $validated['description'] ?? null,
                'version' => $validated['version'] ?? '1.0',
                'status' => 'uploaded',
            ]);

            activity()
                ->performedOn($deliverable)
                ->log('Deliverable uploaded: ' . $originalName);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'redirect' => route('deliverables.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Deliverable $deliverable)
    {
        $deliverable->load(['event.order.customer', 'event.order.lead']);
        return view('deliverables.show', compact('deliverable'));
    }

    public function download(Deliverable $deliverable)
    {
        if (!Storage::disk('public')->exists($deliverable->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($deliverable->file_path, $deliverable->file_name);
    }

    public function updateStatus(Request $request, Deliverable $deliverable)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,uploaded,delivered'
        ]);

        $deliverable->update([
            'status' => $validated['status']
        ]);

        if ($validated['status'] == 'delivered') {
            $deliverable->update(['delivered_at' => now()]);
        }

        activity()
            ->performedOn($deliverable)
            ->log('Deliverable status updated: ' . $validated['status']);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    public function destroy(Deliverable $deliverable)
    {
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($deliverable->file_path)) {
                Storage::disk('public')->delete($deliverable->file_path);
            }

            $fileName = $deliverable->file_name;
            $deliverable->delete();

            activity()
                ->log('Deliverable deleted: ' . $fileName);

            return response()->json([
                'success' => true,
                'message' => 'Deliverable deleted successfully',
                'redirect' => route('deliverables.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting deliverable: ' . $e->getMessage()
            ], 500);
        }
    }
}
