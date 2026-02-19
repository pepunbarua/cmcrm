<?php

namespace App\Http\Controllers;

use App\Models\PackageContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PackageContentController extends Controller
{
    public function index(Request $request)
    {
        $contents = PackageContent::query()
            ->withCount('packageItems')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim();
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('unit', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->input('status') === 'active');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('package_contents.index', compact('contents'));
    }

    public function create()
    {
        return view('package_contents.create');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);

        $content = PackageContent::create($validated);

        activity()
            ->performedOn($content)
            ->causedBy(auth()->user())
            ->log('Package content created');

        return response()->json([
            'success' => true,
            'message' => 'Package content created successfully!',
            'redirect' => route('package-contents.index'),
        ]);
    }

    public function show(PackageContent $packageContent)
    {
        return redirect()->route('package-contents.edit', $packageContent);
    }

    public function edit(PackageContent $packageContent)
    {
        return view('package_contents.edit', compact('packageContent'));
    }

    public function update(Request $request, PackageContent $packageContent): JsonResponse
    {
        $validated = $request->validate($this->rules($packageContent));
        $validated['is_active'] = $request->boolean('is_active', true);

        $packageContent->update($validated);

        activity()
            ->performedOn($packageContent)
            ->causedBy(auth()->user())
            ->log('Package content updated');

        return response()->json([
            'success' => true,
            'message' => 'Package content updated successfully!',
            'redirect' => route('package-contents.index'),
        ]);
    }

    public function destroy(PackageContent $packageContent): JsonResponse
    {
        if ($packageContent->packageItems()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This content is used in one or more packages.',
            ], 422);
        }

        $packageContent->delete();

        activity()
            ->performedOn($packageContent)
            ->causedBy(auth()->user())
            ->log('Package content deleted');

        return response()->json([
            'success' => true,
            'message' => 'Package content deleted successfully!',
        ]);
    }

    protected function rules(?PackageContent $packageContent = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('package_contents', 'name')->ignore($packageContent?->id),
            ],
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ];
    }
}
