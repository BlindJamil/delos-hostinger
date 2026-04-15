<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Employee::query()->ordered();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_it', 'like', "%{$search}%")
                  ->orWhere('role_en', 'like', "%{$search}%")
                  ->orWhere('branch', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->input('status') === 'active');
        }

        $employees = $query->paginate(15)->withQueryString();

        return view('admin.employees.index', compact('employees'));
    }

    public function create(): View
    {
        $employee = new Employee(['active' => true, 'sort_order' => Employee::max('sort_order') + 10]);
        return view('admin.employees.create', compact('employee'));
    }

    public function store(EmployeeRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/employees', 'public');
        } else {
            unset($data['image']);
        }

        $employee = Employee::create($data);

        return redirect()
            ->route('admin.employees.edit', $employee)
            ->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee): View
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(EmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($employee->image && Storage::disk('public')->exists($employee->image)) {
                Storage::disk('public')->delete($employee->image);
            }
            $data['image'] = $request->file('image')->store('uploads/employees', 'public');
        } else {
            unset($data['image']);
        }

        if ($request->boolean('remove_image') && $employee->image) {
            if (Storage::disk('public')->exists($employee->image)) {
                Storage::disk('public')->delete($employee->image);
            }
            $data['image'] = null;
        }

        $employee->update($data);

        return redirect()
            ->route('admin.employees.edit', $employee)
            ->with('success', 'Changes saved.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        if ($employee->image && Storage::disk('public')->exists($employee->image)) {
            Storage::disk('public')->delete($employee->image);
        }

        $employee->delete();

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Employee deleted.');
    }

    public function toggleActive(Employee $employee): RedirectResponse
    {
        $employee->update(['active' => !$employee->active]);

        return back()->with('success', $employee->active ? 'Employee activated.' : 'Employee deactivated.');
    }

    public function reorder(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        foreach ($data['order'] as $index => $id) {
            Employee::where('id', $id)->update(['sort_order' => ($index + 1) * 10]);
        }

        return response()->json(['ok' => true]);
    }
}
