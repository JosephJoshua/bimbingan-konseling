<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Event;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'student_count' => fn () => Student::count(),
            'this_month_consultation_count' => fn () => Consultation::whereMonth('consultation_date', now()->month)->count(),
            'this_month_event_count' => fn () => Event::whereMonth('event_date', now()->month)->count(),
            'consultations_per_category' => fn () => Consultation::select('consultation_categories.name', DB::raw('COUNT(*) AS count'))
                ->join('consultation_categories', 'consultation_categories.id', '=', 'consultations.consultation_category_id')
                ->groupBy('consultations.consultation_category_id')
                ->orderBy('count', 'desc')
                ->get(),
            'consultations_per_day' => fn () => Consultation::select(DB::raw('DATE(consultation_date) AS date'), DB::raw('COUNT(*) as count'))
                ->where(DB::raw('consultation_date'), '>=', now()->subDays(30)->startOfDay())
                ->where(DB::raw('consultation_date'), '<=', now()->endOfDay())
                ->groupBy(DB::raw('consultation_date'))
                ->orderBy(DB::raw('consultation_date'), 'asc')
                ->get(),
            'events' => fn () => Event::select('*', DB::raw('IF(TIMESTAMP(event_date, event_time) >= CURRENT_TIMESTAMP(), "upcoming", "done") as status'))
                ->orderBy('event_date', 'asc')
                ->orderBy('event_time', 'asc')
                ->get(),
            'most_active_students' => fn () => Student::select('students.full_name', DB::raw('COUNT(*) as count'))
                ->join('consultations', 'consultations.student_id', '=', 'students.id')
                ->groupBy('students.id')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
        ]);
    }
}
