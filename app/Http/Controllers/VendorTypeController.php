<?php

namespace App\Http\Controllers;

use App\Models\VendorType;
use Illuminate\Http\Request;

class VendorTypeController extends Controller
{
    public function index()
    {
        $vendorTypes = VendorType::orderBy('order')->get();
        return view('settings.vendor-types.index', compact('vendorTypes'));
    }

    public function create()
    {
        return view('settings.vendor-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'order' => 'integer'
        ]);

        VendorType::create($validated);

        return redirect()->route('settings.vendor-types.index')
            ->with('success', 'Vendor Type created successfully.');
    }

    public function edit(VendorType $vendorType)
    {
        return view('settings.vendor-types.edit', compact('vendorType'));
    }

    public function update(Request $request, VendorType $vendorType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'order' => 'integer'
        ]);

        $vendorType->update($validated);

        return redirect()->route('settings.vendor-types.index')
            ->with('success', 'Vendor Type updated successfully.');
    }

    public function destroy(VendorType $vendorType)
    {
        $vendorType->delete();

        return redirect()->route('settings.vendor-types.index')
            ->with('success', 'Vendor Type deleted successfully.');
    }
}
