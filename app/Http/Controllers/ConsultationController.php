<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationCategory;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ConsultationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Consultation/Index', [
            'data' => fn() => Consultation::with('consultationCategory', 'student')->latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Student $student)
    {
        return Inertia::render('Consultation/Create', [
            'categories' => fn() => ConsultationCategory::orderBy('name')->get(),
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

        $consultation_date = Carbon::parse($request->get('consultation_date'));

        $consultation = $student->consultations()->create([
            ...$request->all(),
            'consultation_date' => $consultation_date,
        ]);

        return redirect()->route('consultations.show', ['consultation' => $consultation->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Consultation $consultation)
    {
        return Inertia::render('Consultation/Show', [
            'consultation' => $consultation->load('consultationCategory', 'student'),
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
            'data' => $consultation->load('consultationCategory', 'student'),
            'categories' => fn() => ConsultationCategory::orderBy('name')->get(),
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

        $consultation_date = Carbon::parse($request->get('consultation_date'));

        $consultation->update([
            ...$request->all(),
            'consultation_date' => $consultation_date,
        ]);

        return redirect()->route('consultations.show', ['consultation' => $consultation->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Consultation $consultation)
    {
        $consultation->delete();
        return redirect()->route('consultations.index');
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
