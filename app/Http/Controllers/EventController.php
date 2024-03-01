<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'sort_by' => ['nullable', 'sometimes', 'string', Rule::in(['title', 'event_date'])],
            'sort_direction' => ['nullable', 'sometimes', 'string', Rule::in(['asc', 'desc'])],
            'search' => ['nullable', 'sometimes', 'string'],
            'status' => ['nullable', 'sometimes', 'string', Rule::in(['all', 'upcoming', 'done'])],
        ]);

        $sortBy = $request->query('sort_by') ?? 'event_date';
        $sortDirection = $request->query('sort_direction') ?? 'desc';
        $statusFilter = $request->query('status') ?? 'all';
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : null;
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;
        $search = strtolower(trim($request->query('search')));

        return Inertia::render('Event/Index', [
            'data' => function () use ($sortBy, $sortDirection, $search, $statusFilter, $startDate, $endDate) {
                return Event::when($search, function ($query) use ($search) {
                    $query->where(DB::raw('LOWER(title)'), 'LIKE', '%' . $search . '%');
                })
                    ->when($statusFilter === 'upcoming', function ($query) {
                        $query->where(DB::raw('TIMESTAMP(event_date, event_time)'), '>=', DB::raw('CURRENT_TIMESTAMP()'));
                    })
                    ->when($statusFilter === 'done', function ($query) {
                        $query->where(DB::raw('TIMESTAMP(event_date, event_time)'), '<', DB::raw('CURRENT_TIMESTAMP()'));
                    })
                    ->when($startDate, function ($query) use ($startDate) {
                        $query->where('event_date', '>=', $startDate);
                    })
                    ->when($endDate, function ($query) use ($endDate) {
                        $query->where('event_date', '<=', $endDate);
                    })
                    ->orderBy($sortBy, $sortDirection)
                    ->latest()
                    ->select('*', DB::raw('IF(TIMESTAMP(event_date, event_time) >= CURRENT_TIMESTAMP(), "upcoming", "done") as status'))
                    ->paginate(10)
                    ->appends(['sort_by' => $sortBy, 'sort_direction' => $sortDirection, 'search' => $search, 'status' => $statusFilter, 'start_date' => $startDate, 'end_date' => $endDate]);
            },
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
            'search' => $request->query('search') ?? '',
            'status_filter' => $statusFilter,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Event/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required'],
            'event_date' => ['required', 'date'],
            'event_time' => ['required', 'date_format:H:i'],
        ]);

        $eventDate = Carbon::parse($request->get('event_date'));

        $event = Event::create([
            ...$request->all(),
            'event_date' => $eventDate,
        ]);

        return redirect()->route('events.show', ['event' => $event->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return Inertia::render('Event/Show', [
            'data' => $event,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Event $event)
    {
        return Inertia::render('Event/Edit', [
            'data' => $event,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required'],
            'event_date' => ['required', 'date'],
            'event_time' => ['required', 'date_format:H:i'],
        ]);

        $eventDate = Carbon::parse($request->get('event_date'));

        $event->update([
            ...$request->all(),
            'event_date' => $eventDate,
        ]);

        return redirect()->route('events.show', ['event' => $event->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image'],
        ]);

        $image = $request->file('image');
        $path = $image->storeAs('event-images', $image->hashName());

        return '/' . $path;
    }
}
