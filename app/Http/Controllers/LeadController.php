<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Imports\LeadsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Lead::with(['vendor', 'user'])
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->vendor_id, function($q) use ($request) {
                $q->where('vendor_id', $request->vendor_id);
            })
            ->when($request->search, function($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('client_name', 'like', '%' . $request->search . '%')
                          ->orWhere('client_phone', 'like', '%' . $request->search . '%')
                          ->orWhere('client_email', 'like', '%' . $request->search . '%');
                });
            })
            ->latest();

        $leads = $query->paginate(15);
        $vendors = Vendor::where('status', 'active')->get();
        
        return view('leads.index', compact('leads', 'vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendors = Vendor::where('status', 'active')->get();
        return view('leads.create', compact('vendors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'event_type' => 'required|in:wedding,birthday,corporate,portrait,other',
            'event_date' => 'nullable|date',
            'budget_range' => 'nullable|string|max:100',
            'package_interest' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:new,contacted,follow_up,qualified,converted,lost',
        ]);

        $validated['user_id'] = Auth::id();

        $lead = Lead::create($validated);
        activity()
            ->performedOn($lead)
            ->causedBy(Auth::user())
            ->log('Lead created');

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully!',
            'redirect' => route('leads.index')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lead $lead)
    {
        $lead->load(['vendor', 'user', 'followUps.user', 'order']);
        return view('leads.show', compact('lead'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lead $lead)
    {
        $vendors = Vendor::where('status', 'active')->get();
        return view('leads.edit', compact('lead', 'vendors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'event_type' => 'required|in:wedding,birthday,corporate,portrait,other',
            'event_date' => 'nullable|date',
            'budget_range' => 'nullable|string|max:100',
            'package_interest' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:new,contacted,follow_up,qualified,converted,lost',
        ]);

        $lead->update($validated);
        activity()
            ->performedOn($lead)
            ->causedBy(Auth::user())
            ->log('Lead updated');

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully!',
            'redirect' => route('leads.index')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        activity()
            ->performedOn($lead)
            ->causedBy(Auth::user())
            ->log('Lead deleted');
            
        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully!'
        ]);
    }

    /**
     * Update lead status via AJAX
     */
    public function updateStatus(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,follow_up,qualified,converted,lost',
        ]);

        $oldStatus = $lead->status;
        $lead->update($validated);

        activity()
            ->performedOn($lead)
            ->causedBy(Auth::user())
            ->log("Lead status changed from {$oldStatus} to {$validated['status']}");

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!'
        ]);
    }

    /**
     * Show bulk import form
     */
    public function importForm()
    {
        return view('leads.import');
    }

    /**
     * Preview leads before import
     */
    public function preview(Request $request)
    {
        try {
            $file = $request->file('file');
            
            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 400);
            }
            
            // Validate file extension
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file type. Only Excel (.xlsx, .xls) and CSV (.csv) files are allowed.'
                ], 422);
            }
            
            // Validate file size (10MB max)
            if ($file->getSize() > 10240 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'File size exceeds 10MB limit.'
                ], 422);
            }
            
            // Read file without importing
            $data = Excel::toArray([], $file)[0];
            
            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File is empty or invalid format'
                ], 400);
            }
            
            $preview = [];
            $headers = array_shift($data); // First row as headers
            
            // Normalize headers (lowercase, trim spaces)
            $headers = array_map(function($h) {
                return strtolower(trim($h));
            }, $headers);
            
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because index starts at 0 and we removed header
                
                // Combine headers with row data
                $rowData = [];
                foreach ($headers as $idx => $header) {
                    $rowData[$header] = isset($row[$idx]) ? $row[$idx] : null;
                }
                
                $errors = $this->validateRow($rowData);
                
                $preview[] = [
                    'row' => $rowNumber,
                    'data' => $rowData,
                    'valid' => empty($errors),
                    'errors' => $errors
                ];
            }
            
            // Store file for later import
            $storedPath = $file->store('temp');
            
            $request->session()->put('import_preview', [
                'filename' => $file->getClientOriginalName(),
                'preview' => $preview,
                'file_path' => $storedPath
            ]);
            
            return response()->json([
                'success' => true,
                'preview' => $preview,
                'total' => count($preview),
                'valid' => count(array_filter($preview, fn($p) => $p['valid'])),
                'invalid' => count(array_filter($preview, fn($p) => !$p['valid']))
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Preview error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Preview failed: ' . $e->getMessage(),
                'file' => basename($e->getFile()),
                'line' => $e->getLine()
            ], 500);
        }
    }
    
    /**
     * Validate a single row
     */
    private function validateRow($row)
    {
        $errors = [];
        
        if (empty($row['client_name'])) {
            $errors[] = 'Client name required';
        }
        if (empty($row['client_phone'])) {
            $errors[] = 'Client phone required';
        }
        
        if (!empty($row['client_email'])) {
            if (!filter_var($row['client_email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email';
            } elseif (Lead::where('client_email', $row['client_email'])->exists()) {
                $errors[] = 'Email exists';
            }
        }
        
        if (!empty($row['vendor_id']) && !Vendor::where('id', $row['vendor_id'])->exists()) {
            $errors[] = 'Invalid vendor ID';
        }
        
        if (!empty($row['event_type'])) {
            $validTypes = ['wedding', 'birthday', 'corporate', 'portrait', 'other'];
            if (!in_array($row['event_type'], $validTypes)) {
                $errors[] = 'Invalid event type';
            }
        }
        
        if (!empty($row['status'])) {
            $validStatuses = ['new', 'contacted', 'follow_up', 'qualified', 'converted', 'lost'];
            if (!in_array($row['status'], $validStatuses)) {
                $errors[] = 'Invalid status';
            }
        }
        
        if (!empty($row['event_date'])) {
            try {
                \Carbon\Carbon::parse($row['event_date']);
            } catch (\Exception $e) {
                $errors[] = 'Invalid date';
            }
        }
        
        return $errors;
    }

    /**
     * Handle bulk import
     */
    public function import(Request $request)
    {
        try {
            $previewData = $request->session()->get('import_preview');
            
            if (!$previewData) {
                return response()->json([
                    'success' => false,
                    'message' => 'No preview data found'
                ], 400);
            }
            
            $filePath = Storage::path($previewData['file_path']);
            
            Excel::import(new LeadsImport, $filePath);
            
            Storage::delete($previewData['file_path']);
            $request->session()->forget('import_preview');
            
            activity()
                ->causedBy(Auth::user())
                ->log('Bulk imported leads from file: ' . $previewData['filename']);
            
            return response()->json([
                'success' => true,
                'message' => 'Leads imported successfully!',
                'redirect' => route('leads.index')
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Import failed with validation errors',
                'errors' => $errors
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download sample template
     */
    public function downloadTemplate()
    {
        $headers = [
            'vendor_id',
            'client_name',
            'client_email',
            'client_phone',
            'event_type',
            'event_date',
            'budget_range',
            'package_interest',
            'notes',
            'status'
        ];

        $sampleData = [
            [
                '1',
                'John Doe',
                'john@example.com',
                '01712345678',
                'wedding',
                '2025-12-25',
                '50000-100000',
                'Premium Package',
                'Sample lead for wedding photography',
                'new'
            ]
        ];

        $filename = 'leads_import_template.csv';
        $handle = fopen('php://temp', 'r+');
        
        // Write headers
        fputcsv($handle, $headers);
        
        // Write sample data
        foreach ($sampleData as $row) {
            fputcsv($handle, $row);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
