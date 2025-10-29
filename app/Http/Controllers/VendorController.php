<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::latest()->paginate(15);
        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_name' => 'required|max:255',
            'vendor_type' => 'required|in:wedding_venue,convention_hall,community_center,hotel,other',
            'phone' => 'required',
            'email' => 'nullable|email',
            'city' => 'nullable',
            'address' => 'nullable',
            'contact_person' => 'nullable',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $vendor = Vendor::create($validated);

        activity()
            ->performedOn($vendor)
            ->causedBy(auth()->user())
            ->log('Vendor created');

        return response()->json([
            'success' => true,
            'message' => 'Vendor created successfully!',
            'redirect' => route('vendors.index'),
        ]);
    }

    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'vendor_name' => 'required|max:255',
            'vendor_type' => 'required|in:wedding_venue,convention_hall,community_center,hotel,other',
            'phone' => 'required',
            'email' => 'nullable|email',
            'city' => 'nullable',
            'address' => 'nullable',
            'contact_person' => 'nullable',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $vendor->update($validated);

        activity()
            ->performedOn($vendor)
            ->causedBy(auth()->user())
            ->log('Vendor updated');

        return response()->json([
            'success' => true,
            'message' => 'Vendor updated successfully!',
            'redirect' => route('vendors.index'),
        ]);
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        activity()
            ->performedOn($vendor)
            ->causedBy(auth()->user())
            ->log('Vendor deleted');

        return response()->json([
            'success' => true,
            'message' => 'Vendor deleted successfully!',
        ]);
    }
}
