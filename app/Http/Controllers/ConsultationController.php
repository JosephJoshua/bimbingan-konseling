<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationCategory;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ConsultationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'sort_by' => ['nullable', 'sometimes', 'string', Rule::in(['students.full_name', 'consultation_categories.name', 'consultation_date'])],
            'sort_direction' => ['nullable', 'sometimes', 'string', Rule::in(['asc', 'desc'])],
            'search' => ['nullable', 'sometimes', 'string'],
        ]);

        $sortBy = $request->query('sort_by') ?? 'students.full_name';
        $sortDirection = $request->query('sort_direction') ?? 'asc';
        $search = strtolower(trim($request->query('search')));

        return Inertia::render('Consultation/Index', [
            'data' => fn () => Consultation::with('consultationCategory', 'student')->latest()->paginate(10),
            'data' => function () use ($sortBy, $sortDirection, $search) {
                return Consultation::when($search, function ($query) use ($search) {
                    $query->where(DB::raw('LOWER(students.full_name)'), 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(consultation_categories.name)'), 'LIKE', "%{$search}%");
                })
                    ->with('consultationCategory', 'student')
                    ->join('consultation_categories', 'consultation_categories.id', '=', 'consultations.consultation_category_id')
                    ->join('students', 'students.id', '=', 'consultations.student_id')
                    ->orderBy(DB::raw($sortBy), $sortDirection)
                    ->select('consultations.*')
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
    public function create(Student $student)
    {
        return Inertia::render('Consultation/Create', [
            'categories' => fn () => ConsultationCategory::orderBy('name')->get(),
            'student' => $student,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Student $student)
    {
        $request->validate([
            'consultation_category_id' => ['required', Rule::exists('consultation_categories', 'id')],
            'description' => ['required'],
            'consultation_date' => ['required', 'date'],
        ]);

        $consultationDate = Carbon::parse($request->get('consultation_date'));

        $consultation = $student->consultations()->create([
            ...$request->all(),
            'consultation_date' => $consultationDate->startOfDay()->shiftTimezone('UTC'),
        ]);

        return redirect()->route('consultations.show', ['consultation' => $consultation->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Consultation $consultation)
    {
        return Inertia::render('Consultation/Show', [
            'consultation' => [
                ...$consultation->load('consultationCategory', 'student')->toArray(),
                'consultation_date' => Carbon::parse($consultation->consultation_date)->getTimestampMs(),
            ],
            'index' => Consultation::where('student_id', $consultation->student_id)
                ->where('consultation_date', '<', $consultation->consultation_date)
                ->latest()
                ->count() + 1,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Consultation $consultation)
    {
        return Inertia::render('Consultation/Edit', [
            'data' => [
                ...$consultation->load('consultationCategory', 'student')->toArray(),
                'consultation_date' => Carbon::parse($consultation->consultation_date)->getTimestampMs(),
            ],
            'categories' => fn () => ConsultationCategory::orderBy('name')->get(),
            'index' => Consultation::where('student_id', $consultation->student_id)
                ->where('consultation_date', '<', $consultation->consultation_date)
                ->latest()
                ->count() + 1,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Consultation $consultation)
    {
        $request->validate([
            'consultation_category_id' => ['required', Rule::exists('consultation_categories', 'id')],
            'description' => ['required'],
            'consultation_date' => ['required', 'date'],
        ]);

        $consultationDate = Carbon::parse($request->get('consultation_date'));

        $consultation->update([
            ...$request->all(),
            'consultation_date' => $consultationDate->startOfDay()->shiftTimezone('UTC'),
        ]);

        return redirect()->route('consultations.show', ['consultation' => $consultation->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Consultation $consultation)
    {
        $consultation->delete();
        return redirect()->back();
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image'],
        ]);

        $image = $request->file('image');
        $path = $image->storeAs('consultation-images', $image->hashName());

        return '/' . $path;
    }
}