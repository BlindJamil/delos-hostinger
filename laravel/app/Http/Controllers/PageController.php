<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Service;

class PageController extends Controller
{
    public function home()
    {
        $featuredEmployees = Employee::query()->active()->ordered()->get();

        return view('pages.home', compact('featuredEmployees'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function services()
    {
        $services = Service::query()->active()->ordered()->get();

        return view('pages.services', compact('services'));
    }

    public function projects()
    {
        $projects = Project::query()->active()->ordered()->get();
        $heroProjects = $projects->where('featured', true)->values();

        // Derive filter options from the project types that actually exist in
        // the DB. Label is picked from the first project of each type (falls
        // back to the machine key if no label was set).
        $filters = $projects
            ->filter(fn ($p) => $p->type)
            ->groupBy('type')
            ->map(fn ($group) => $group->first()->localized('type_label') ?: ucfirst($group->first()->type))
            ->prepend(__('projects.filters.all'), 'all');

        return view('pages.projects', compact('projects', 'heroProjects', 'filters'));
    }

    public function brands()
    {
        $brands = Brand::query()->active()->ordered()->get();

        return view('pages.brands', compact('brands'));
    }

    public function branches()
    {
        $branches = Branch::query()
            ->active()
            ->ordered()
            ->get();

        return view('pages.branches', compact('branches'));
    }

    public function contact()
    {
        return view('pages.contact');
    }
}
