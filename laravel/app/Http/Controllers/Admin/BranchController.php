<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BranchRequest;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(Request $request): View
    {
        $query = Branch::query()->ordered();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('city_key', 'like', "%{$search}%")
                  ->orWhere('address_en', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->input('status') === 'active');
        }

        $branches = $query->paginate(20)->withQueryString();

        return view('admin.branches.index', compact('branches'));
    }

    public function create(): View
    {
        $branch = new Branch([
            'active' => true,
            'is_flagship' => false,
            'sort_order' => Branch::max('sort_order') + 10,
            'hours_en' => 'Sat – Thu: 10:00 – 20:00',
        ]);
        return view('admin.branches.create', compact('branch'));
    }

    public function store(BranchRequest $request): RedirectResponse
    {
        $data = $this->handleImage($request, null);

        // Only one flagship at a time — if this one is set flagship, demote others.
        if (($data['is_flagship'] ?? false)) {
            Branch::where('is_flagship', true)->update(['is_flagship' => false]);
        }

        $branch = Branch::create($data);

        return redirect()
            ->route('admin.branches.edit', $branch)
            ->with('success', 'Branch created successfully.');
    }

    public function edit(Branch $branch): View
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(BranchRequest $request, Branch $branch): RedirectResponse
    {
        $data = $this->handleImage($request, $branch);

        if (($data['is_flagship'] ?? false) && !$branch->is_flagship) {
            Branch::where('id', '!=', $branch->id)
                ->where('is_flagship', true)
                ->update(['is_flagship' => false]);
        }

        $branch->update($data);

        return redirect()
            ->route('admin.branches.edit', $branch)
            ->with('success', 'Changes saved.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        if ($branch->image && str_starts_with($branch->image, 'uploads/')
            && Storage::disk('public')->exists($branch->image)) {
            Storage::disk('public')->delete($branch->image);
        }

        $branch->delete();

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'Branch deleted.');
    }

    public function toggleActive(Branch $branch): RedirectResponse
    {
        $branch->update(['active' => !$branch->active]);

        return back()->with('success', $branch->active ? 'Branch activated.' : 'Branch deactivated.');
    }

    /**
     * Shared image handling across store + update. Uploads new file, deletes
     * old one on replacement, honours the remove_image flag. Returns the
     * sanitized $data array ready for Eloquent create/update.
     */
    private function handleImage(BranchRequest $request, ?Branch $existing): array
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($existing && $existing->image && str_starts_with($existing->image, 'uploads/')
                && Storage::disk('public')->exists($existing->image)) {
                Storage::disk('public')->delete($existing->image);
            }
            $data['image'] = $request->file('image')->store('uploads/branches', 'public');
        } else {
            unset($data['image']);
        }

        if ($request->boolean('remove_image') && $existing && $existing->image) {
            if (str_starts_with($existing->image, 'uploads/')
                && Storage::disk('public')->exists($existing->image)) {
                Storage::disk('public')->delete($existing->image);
            }
            $data['image'] = null;
        }

        return $data;
    }
}
