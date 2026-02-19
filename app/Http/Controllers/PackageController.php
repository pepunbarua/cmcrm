<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = Package::query()
            ->withCount('items')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim();
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->input('status') === 'active');
            })
            ->when($request->filled('pricing_mode'), function ($query) use ($request) {
                $query->where('pricing_mode', $request->input('pricing_mode'));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('packages.index', compact('packages'));
    }

    public function create()
    {
        $contents = PackageContent::active()->orderBy('name')->get();
        return view('packages.create', compact('contents'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatePackage($request);

        $package = DB::transaction(function () use ($validated, $request) {
            $package = Package::create([
                'name' => $validated['name'],
                'code' => $validated['code'] ?? null,
                'description' => $validated['description'] ?? null,
                'pricing_mode' => $validated['pricing_mode'],
                'base_price' => $validated['base_price'],
                'is_active' => $request->boolean('is_active', true),
                'created_by' => auth()->id(),
            ]);

            $this->syncItems($package, $validated['items'] ?? []);

            return $package;
        });

        activity()
            ->performedOn($package)
            ->causedBy(auth()->user())
            ->log('Package created');

        return response()->json([
            'success' => true,
            'message' => 'Package created successfully!',
            'redirect' => route('packages.index'),
        ]);
    }

    public function show(Package $package)
    {
        return redirect()->route('packages.edit', $package);
    }

    public function edit(Package $package)
    {
        $package->load('items');
        $contents = PackageContent::active()->orderBy('name')->get();

        return view('packages.edit', compact('package', 'contents'));
    }

    public function update(Request $request, Package $package): JsonResponse
    {
        $validated = $this->validatePackage($request, $package);

        DB::transaction(function () use ($validated, $request, $package) {
            $package->update([
                'name' => $validated['name'],
                'code' => $validated['code'] ?? null,
                'description' => $validated['description'] ?? null,
                'pricing_mode' => $validated['pricing_mode'],
                'base_price' => $validated['base_price'],
                'is_active' => $request->boolean('is_active', true),
            ]);

            $this->syncItems($package, $validated['items'] ?? []);
        });

        activity()
            ->performedOn($package)
            ->causedBy(auth()->user())
            ->log('Package updated');

        return response()->json([
            'success' => true,
            'message' => 'Package updated successfully!',
            'redirect' => route('packages.index'),
        ]);
    }

    public function destroy(Package $package): JsonResponse
    {
        $package->delete();

        activity()
            ->performedOn($package)
            ->causedBy(auth()->user())
            ->log('Package deleted');

        return response()->json([
            'success' => true,
            'message' => 'Package deleted successfully!',
        ]);
    }

    protected function validatePackage(Request $request, ?Package $package = null): array
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('packages', 'code')->ignore($package?->id),
            ],
            'description' => 'nullable|string',
            'pricing_mode' => 'required|in:fixed,item_sum,hybrid',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
            'items' => 'nullable|array',
            'items.*.package_content_id' => 'required|integer|distinct|exists:package_contents,id',
            'items.*.default_qty' => 'required|numeric|gt:0',
            'items.*.default_unit_price' => 'nullable|numeric|min:0',
            'items.*.is_mandatory' => 'required|boolean',
            'items.*.is_editable' => 'required|boolean',
            'items.*.sort_order' => 'nullable|integer|min:0',
        ]);

        $validator->after(function ($validator) use ($request) {
            $requiresItems = in_array($request->input('pricing_mode'), ['item_sum', 'hybrid'], true);
            $items = $request->input('items', []);

            if ($requiresItems && empty($items)) {
                $validator->errors()->add('items', 'At least one package content item is required.');
            }
        });

        return $validator->validate();
    }

    protected function syncItems(Package $package, array $items): void
    {
        $package->items()->delete();

        if (empty($items)) {
            return;
        }

        $contentIds = collect($items)
            ->pluck('package_content_id')
            ->unique()
            ->values();

        $contentMap = PackageContent::whereIn('id', $contentIds)->pluck('name', 'id');

        $payload = collect($items)
            ->values()
            ->map(function (array $item, int $index) use ($contentMap) {
                $contentId = (int) $item['package_content_id'];
                $unitPrice = $item['default_unit_price'] ?? null;
                if ($unitPrice === '') {
                    $unitPrice = null;
                }

                return [
                    'package_content_id' => $contentId,
                    'content_name_snapshot' => $contentMap->get($contentId, 'Unknown Content'),
                    'default_qty' => $item['default_qty'],
                    'default_unit_price' => $unitPrice,
                    'is_mandatory' => (bool) $item['is_mandatory'],
                    'is_editable' => (bool) $item['is_editable'],
                    'sort_order' => $item['sort_order'] ?? $index,
                ];
            })
            ->all();

        $package->items()->createMany($payload);
    }
}
