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
            'sort_by' => ['nullable', 'sometimes', 'string', Rule::in(['title', 'start_date', 'end_date'])],
            'sort_direction' => ['nullable', 'sometimes', 'string', Rule::in(['asc', 'desc'])],
            'search' => ['nullable', 'sometimes', 'string'],
            'status' => ['nullable', 'sometimes', 'string', Rule::in(['all',  'ongoing', 'upcoming', 'done'])],
            'start_date' => ['nullable', 'sometimes', 'numeric'],
            'end_date' => ['nullable', 'sometimes', 'numeric', 'gte:end_date'],
        ]);

        // dd($request->query('end_date'), Carbon::createFromTimestampUTC($request->query('end_date')));

        $sortBy = $request->query('sort_by') ?? 'start_date';
        $sortDirection = $request->query('sort_direction') ?? 'desc';
        $statusFilter = $request->query('status') ?? 'all';
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = strtolower(trim($request->query('search')));

        // dd(Carbon::createFromTimestampUTC($request->query('end_date')));

        return Inertia::render('Event/Index', [
            'data' => function () use ($sortBy, $sortDirection, $search, $statusFilter, $startDate, $endDate) {
                return Event::when($search, function ($query) use ($search) {
                    $query->where(DB::raw('LOWER(title)'), 'LIKE', '%' . $search . '%');
                })
                    ->when($statusFilter === 'upcoming', function ($query) {
                        $query->where('start_date', '>=', DB::raw('CURRENT_TIMESTAMP()'));
                    })
                    ->when($statusFilter === 'ongoing', function ($query) {
                        $query->where('start_date', '<=', DB::raw('CURRENT_TIMESTAMP()'))
                            ->where('end_date', '>=', DB::raw('CURRENT_TIMESTAMP()'));
                    })
                    ->when($statusFilter === 'done', function ($query) {
                        $query->where('end_date', '<', DB::raw('CURRENT_TIMESTAMP()'));
                    })
                    ->when($startDate != null && $endDate != null, function ($query) use ($startDate, $endDate) {
                        // The event must not end before the filter start date.
                        $query->whereNot('end_date', '<', Carbon::createFromTimestamp($startDate)->startOfDay());

                        // The event must not start after the filter end date.
                        $query->whereNot('start_date', '>', Carbon::createFromTimestamp($endDate)->endOfDay());
                    })
                    ->orderBy($sortBy, $sortDirection)
                    ->latest()
                    ->select('*', DB::raw('CASE
                        WHEN start_date >= CURRENT_TIMESTAMP() THEN "upcoming"
                        WHEN start_date <= CURRENT_TIMESTAMP() AND end_date >= CURRENT_TIMESTAMP() THEN "ongoing"
                        WHEN end_date < CURRENT_TIMESTAMP() THEN "done"
                    END as status'))
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
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = Carbon::parse($request->get('start_date'));
        $endDate = Carbon::parse($request->get('end_date'));

        $event = Event::create([
            ...$request->all(),
            'start_date' => $startDate->setTimezone('UTC'),
            'end_date' => $endDate->setTimezone('UTC'),
        ]);

        return redirect()->route('events.show', ['event' => $event->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $data = Event::where('id', '=', $event->id)
            ->select('*', DB::raw('CASE
                    WHEN start_date >= CURRENT_TIMESTAMP() THEN "upcoming"
                    WHEN start_date <= CURRENT_TIMESTAMP() AND end_date >= CURRENT_TIMESTAMP() THEN "ongoing"
                    WHEN end_date < CURRENT_TIMESTAMP() THEN "done"
                END as status'))
            ->first();

        return Inertia::render('Event/Show', [
            'data' => $data,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Event $event)
    {
        return Inertia::render('Event/Edit', [
            'data' => [
                ...$event->toArray(),
                'start_date' => $event->start_date->getTimestampMs(),
                'end_date' => $event->end_date->getTimestampMs(),
            ],
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
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = Carbon::parse($request->get('start_date'));
        $endDate = Carbon::parse($request->get('end_date'));

        $event->update([
            ...$request->all(),
            'start_date' => $startDate->setTimezone('UTC'),
            'end_date' => $endDate->setTimezone('UTC'),
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