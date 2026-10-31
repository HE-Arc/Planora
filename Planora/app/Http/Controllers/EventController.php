<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveEventRequest;
use App\Models\Event;
use App\Models\Location;
use App\Models\Tag;
use App\Models\User;

class EventController extends Controller
{
    /**
     * Display the specified event.
     */
    public function show(Event $event) {
        $event->load(['location', 'organizer', 'tags']);

        return view('events.form', [
            'mode' => 'view',
            'event' => $event,
            'locations' => Location::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function create() {
        return view('events.form', [
            'mode' => 'create',
            'event' => new Event(),
            'locations' => Location::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = \App\Models\Event::latest()->get();
        return view('home', ['events' => $events]);
    }

    /**
     * Create a new event.
     */
    public function store(SaveEventRequest $request)
    {
        //To-do Later : use real logged user
        $organizer = User::where('email', 'Sacha.Gruring@he-arc.ch')->first();

        abort_if(
            $organizer === null,
            503,
            "demo user not in database"
        );

        $event = new Event($request->validated());
        $event->organizer()->associate($organizer);
        $event->status = 'open';
        $event->save();

        return redirect()->route('events.show', $event)
            ->with('success','Event created successfully.');
    }

    public function edit(Event $event) {
        $event->load(['location', 'organizer', 'tags']);

        return view('events.form', [
            'mode' => 'edit',
            'event' => $event,
            'locations' => Location::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified event.
     */
    public function update(SaveEventRequest $request, Event $event)
    {
        $event->update($request->validated());
        return redirect()->route('events.show', $event)
            ->with('success','Event updated successfully');
    }

    /**
     * Remove the specified event.
     */
    public function destroy(string $id)
    {
        $event = \App\Models\Event::find($id);
        $event->delete();
        return redirect()->route('events.index')
            ->with('success','Event deleted successfully');
    }

    public function publish()
    {

    }

    public function search()
    {
        
    }
}
