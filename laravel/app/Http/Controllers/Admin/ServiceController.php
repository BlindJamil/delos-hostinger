<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesHybridImages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ServiceController extends Controller
{
    use HandlesHybridImages;

    public function index(Request $request): View
    {
        $query = Service::query()->ordered();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_it', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->input('status') === 'active');
        }

        $services = $query->paginate(15)->withQueryString();

        return view('admin.services.index', compact('services'));
    }

    public function create(): View
    {
        $next = Service::max('sort_order') + 10;
        $service = new Service([
            'active' => true,
            'sort_order' => $next,
            'num' => str_pad((string) (int) ($next / 10), 2, '0', STR_PAD_LEFT),
        ]);
        return view('admin.services.create', compact('service'));
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/services', 'public');
        } else {
            unset($data['image']);
        }

        $service = Service::create($data);
        $this->applyHybridImageFields($service, $request, 'uploads/services');

        return redirect()
            ->route('admin.services.edit', $service)
            ->with('success', 'Service created successfully.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($service->image && str_starts_with($service->image, 'uploads/')
                && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('uploads/services', 'public');
        } else {
            unset($data['image']);
        }

        if ($request->boolean('remove_image') && $service->image) {
            if (str_starts_with($service->image, 'uploads/')
                && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = null;
        }

        $service->update($data);
        $this->applyHybridImageFields($service, $request, 'uploads/services');

        return redirect()
            ->route('admin.services.edit', $service)
            ->with('success', 'Changes saved.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        if ($service->image && str_starts_with($service->image, 'uploads/')
            && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }
        $this->deleteHybridImageFiles($service);

        $service->delete();

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service deleted.');
    }

    public function toggleActive(Service $service): RedirectResponse
    {
        $service->update(['active' => !$service->active]);

        return back()->with('success', $service->active ? 'Service activated.' : 'Service deactivated.');
    }
}
