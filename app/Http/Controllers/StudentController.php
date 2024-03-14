<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'sort_by' => ['nullable', 'sometimes', 'string', Rule::in(['nis', 'nisn', 'full_name', 'gender', 'religion', 'phone_number', 'email', 'full_address', 'origin_school'])],
            'sort_direction' => ['nullable', 'sometimes', 'string', Rule::in(['asc', 'desc'])],
            'search' => ['nullable', 'sometimes', 'string'],
        ]);

        $sortBy = $request->query('sort_by') ?? 'full_name';
        $sortDirection = $request->query('sort_direction') ?? 'asc';
        $search = strtolower(trim($request->query('search')));

        return Inertia::render('Student/Index', [
            'data' => function () use ($sortBy, $sortDirection, $search) {
                return Student::when($search, function ($query) use ($search) {
                    $query->where(DB::raw('LOWER(nis)'), 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(nisn)'), 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(full_name)'), 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(full_address)'), 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(origin_school)'), 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(phone_number)'), 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(email)'), 'LIKE', "%{$search}%");
                })
                    ->with('consultations', 'achievements')
                    ->orderBy($sortBy, $sortDirection)
                    ->latest()
                    ->paginate(10)
                    ->appends(['sort_by' => $sortBy, 'sort_direction' => $sortDirection, 'search' => $search]);
            },
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
            'search' => $request->query('search'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Student/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nisn' => ['required', 'string', 'max:10', Rule::unique('students', 'nisn')],
            'nis' => ['required', 'string', 'max:16', Rule::unique('students', 'nis')],
            'nik' => ['required', 'string', 'max:16', Rule::unique('students', 'nik')],
            'gender' => ['required', 'string', Rule::in('male', 'female')],
            'religion' => ['required', 'string', Rule::in('muslim', 'catholicism', 'christianity', 'hindu', 'buddhism', 'confucianism', 'other')],
            'full_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'birth_place' => ['required', 'string', 'max:255'],
            'status_in_family' => ['required', 'string', Rule::in('biological', 'adopted', 'stepchild')],
            'child_order' => ['required', 'integer', 'min:1'],
            'full_address' => ['required', 'string', 'max:255'],
            'origin_school' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'father_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'father_phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'mother_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'mother_phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'guardian_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'guardian_phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
        ]);

        $birth_date = Carbon::parse($request->get('birth_date'));

        Student::create([
            ...$request->all(),
            'birth_date' => $birth_date->startOfDay()->shiftTimezone('UTC'),
        ]);

        return redirect()->route('students.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        return Inertia::render('Student/Edit', [
            'data' => [
                ...$student->toArray(),
                'birth_date' => Carbon::parse($student->birth_date)->getTimestampMs(),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nisn' => ['required', 'string', 'max:10', Rule::unique('students', 'nisn')->ignore($student->id)],
            'nis' => ['required', 'string', 'max:16', Rule::unique('students', 'nis')->ignore($student->id)],
            'nik' => ['required', 'string', 'max:16', Rule::unique('students', 'nik')->ignore($student->id)],
            'gender' => ['required', 'string', Rule::in('male', 'female')],
            'religion' => ['required', 'string', Rule::in('muslim', 'catholicism', 'christianity', 'hindu', 'buddhism', 'confucianism', 'other')],
            'full_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'birth_place' => ['required', 'string', 'max:255'],
            'status_in_family' => ['required', 'string', Rule::in('biological', 'adopted', 'stepchild')],
            'child_order' => ['required', 'integer', 'min:1'],
            'full_address' => ['required', 'string', 'max:255'],
            'origin_school' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'father_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'father_phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'mother_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'mother_phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'guardian_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'guardian_phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
        ]);

        $birth_date = Carbon::parse($request->get('birth_date'));

        $student->update([
            ...$request->all(),
            'birth_date' => $birth_date->startOfDay()->shiftTimezone('UTC'),
        ]);

        return redirect()->route('students.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index');
    }
}