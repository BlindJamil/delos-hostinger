<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(Request $request): View
    {
        $query = Brand::query()->ordered();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('category_en', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->input('status') === 'active');
        }

        $brands = $query->paginate(15)->withQueryString();

        return view('admin.brands.index', compact('brands'));
    }

    public function create(): View
    {
        $brand = new Brand([
            'active' => true,
            'sort_order' => Brand::max('sort_order') + 10,
        ]);
        return view('admin.brands.create', compact('brand'));
    }

    public function store(BrandRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/brands', 'public');
        } else {
            unset($data['image']);
        }

        $brand = Brand::create($data);

        return redirect()
            ->route('admin.brands.edit', $brand)
            ->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(BrandRequest $request, Brand $brand): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($brand->image && str_starts_with($brand->image, 'uploads/')
                && Storage::disk('public')->exists($brand->image)) {
                Storage::disk('public')->delete($brand->image);
            }
            $data['image'] = $request->file('image')->store('uploads/brands', 'public');
        } else {
            unset($data['image']);
        }

        if ($request->boolean('remove_image') && $brand->image) {
            if (str_starts_with($brand->image, 'uploads/')
                && Storage::disk('public')->exists($brand->image)) {
                Storage::disk('public')->delete($brand->image);
            }
            $data['image'] = null;
        }

        $brand->update($data);

        return redirect()
            ->route('admin.brands.edit', $brand)
            ->with('success', 'Changes saved.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->image && str_starts_with($brand->image, 'uploads/')
            && Storage::disk('public')->exists($brand->image)) {
            Storage::disk('public')->delete($brand->image);
        }

        $brand->delete();

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand deleted.');
    }

    public function toggleActive(Brand $brand): RedirectResponse
    {
        $brand->update(['active' => !$brand->active]);

        return back()->with('success', $brand->active ? 'Brand activated.' : 'Brand deactivated.');
    }
}
