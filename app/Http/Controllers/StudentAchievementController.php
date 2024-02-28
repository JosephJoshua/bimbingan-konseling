<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAchievement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentAchievementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Student $student)
    {
        return Inertia::render('StudentAchievement/Create', [
            'student' => $student,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Student $student, Request $request)
    {
        $request->validate([
            'title' => ['required', 'string'],
        ]);

        $student->achievements()->create($request->all());
        return redirect()->route('students.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentAchievement $studentAchievement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentAchievement $studentAchievement)
    {
        return Inertia::render('StudentAchievement/Edit', [
            'achievement' => $studentAchievement->load('student'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentAchievement $studentAchievement)
    {
        $request->validate([
            'title' => ['required', 'string'],
        ]);

        $studentAchievement->update($request->all());
        return redirect()->route('students.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentAchievement $studentAchievement)
    {
        $studentAchievement->delete();
        return redirect()->back();
    }
}
