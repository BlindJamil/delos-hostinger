<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Support\Collection;

class PageController extends Controller
{
    /**
     * Run an Eloquent query, returning an empty collection if the table
     * doesn't exist yet. Protects the public site from 500-ing on a
     * fresh deploy before `php artisan migrate` has been run.
     */
    private function safeQuery(callable $fn): Collection
    {
        try {
            return $fn();
        } catch (\Throwable) {
            return collect();
        }
    }

    public function home()
    {
        $featuredEmployees = $this->safeQuery(
            fn () => Employee::query()->active()->ordered()->get()
        );

        return view('pages.home', compact('featuredEmployees'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function services()
    {
        $services = $this->safeQuery(
            fn () => Service::query()->active()->ordered()->get()
        );

        return view('pages.services', compact('services'));
    }

    public function projects()
    {
        $projects = $this->safeQuery(
            fn () => Project::query()->active()->ordered()->get()
        );
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
        $brands = $this->safeQuery(
            fn () => Brand::query()->active()->ordered()->get()
        );

        return view('pages.brands', compact('brands'));
    }

    public function branches()
    {
        $branches = $this->safeQuery(
            fn () => Branch::query()->active()->ordered()->get()
        );

        return view('pages.branches', compact('branches'));
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function showEmployee(string $locale, int $employee)
    {
        $employee = Employee::query()->where('id', $employee)->where('active', true)->first();

        if (!$employee) {
            abort(404);
        }

        return view('pages.employee-show', compact('employee'));
    }
}
