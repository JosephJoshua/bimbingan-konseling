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
        ]);

        $sortBy = $request->query('sort_by') ?? 'event_date';
        $sortDirection = $request->query('sort_direction') ?? 'asc';
        $search = strtolower(trim($request->query('search')));

        return Inertia::render('Event/Index', [
            'data' => function () use ($sortBy, $sortDirection, $search) {
                return Event::when($search, function ($query) use ($search) {
                    $query->where(DB::raw('LOWER(title)', 'LIKE', '%' . $search . '%'));
                })
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

        $eventDate = Carbon::parse($request->get('consultation_date'));

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

        $eventDate = Carbon::parse($request->get('consultation_date'));

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
