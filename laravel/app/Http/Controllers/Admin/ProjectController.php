<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $query = Project::query()->ordered();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title_en', 'like', "%{$search}%")
                  ->orWhere('title_ar', 'like', "%{$search}%")
                  ->orWhere('title_it', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->input('status') === 'active');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('featured')) {
            $query->where('featured', $request->input('featured') === 'yes');
        }

        $projects = $query->paginate(15)->withQueryString();
        $types = Project::query()->whereNotNull('type')->distinct()->pluck('type');

        return view('admin.projects.index', compact('projects', 'types'));
    }

    public function create(): View
    {
        $project = new Project([
            'active' => true,
            'featured' => false,
            'sort_order' => Project::max('sort_order') + 10,
        ]);
        return view('admin.projects.create', compact('project'));
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/projects', 'public');
        } else {
            unset($data['image']);
        }

        $project = Project::create($data);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', 'Project created successfully.');
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($project->image && str_starts_with($project->image, 'uploads/')
                && Storage::disk('public')->exists($project->image)) {
                Storage::disk('public')->delete($project->image);
            }
            $data['image'] = $request->file('image')->store('uploads/projects', 'public');
        } else {
            unset($data['image']);
        }

        if ($request->boolean('remove_image') && $project->image) {
            if (str_starts_with($project->image, 'uploads/')
                && Storage::disk('public')->exists($project->image)) {
                Storage::disk('public')->delete($project->image);
            }
            $data['image'] = null;
        }

        $project->update($data);

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', 'Changes saved.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        if ($project->image && str_starts_with($project->image, 'uploads/')
            && Storage::disk('public')->exists($project->image)) {
            Storage::disk('public')->delete($project->image);
        }

        $project->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project deleted.');
    }

    public function toggleActive(Project $project): RedirectResponse
    {
        $project->update(['active' => !$project->active]);

        return back()->with('success', $project->active ? 'Project activated.' : 'Project deactivated.');
    }

    public function toggleFeatured(Project $project): RedirectResponse
    {
        $project->update(['featured' => !$project->featured]);

        return back()->with('success', $project->featured ? 'Marked as featured.' : 'Removed from featured.');
    }
}
