<?php

namespace App\Http\Controllers;

use App\Models\ConsultationCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ConsultationCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('ConsultationCategory/Index', [
            'data' => fn() => ConsultationCategory::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('ConsultationCategory/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', Rule::unique('consultation_categories', 'name')],
        ]);

        ConsultationCategory::create($request->all());
        return redirect()->route('consultation-categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(ConsultationCategory $consultationCategory)
    {
        return Inertia::render('ConsultationCategory/Show', [
            'data' => $consultationCategory
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ConsultationCategory $consultationCategory)
    {
        return Inertia::render('ConsultationCategory/Edit', [
            'data' => $consultationCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ConsultationCategory $consultationCategory)
    {
        $request->validate([
            'name' => ['required', Rule::unique('consultation_categories', 'name')->ignore($consultationCategory->id)],
        ]);

        $consultationCategory->update($request->all());
        return redirect()->route('consultation-categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConsultationCategory $consultationCategory)
    {
        $consultationCategory->delete();
        return redirect()->route('consultation-categories.index');
    }
}