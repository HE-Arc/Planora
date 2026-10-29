<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
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
    public function create(Request $request)
    {
        \App\Models\Event::create(
            $request->validate([
                'location_id' => 'required|exists:locations,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'max_participants' => 'required|integer|min:1',
                'min_age' => 'required|integer|min:0',
                'starts_at' => 'required|date',
                'ends_at' => 'required|date|after:starts_at',
            ])
        );
        return redirect()->route('events.index')
            ->with('success','Event created successfully.');
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, string $id)
    {
        \App\Models\Event::findOrFail($id)->update($request->all());
        return redirect()->route('events.index')
            ->with('success','Event updated successfully');
    }

    /**
     * Remove the specified event.
     */
    public function delete(string $id)
    {
        $event = \App\Models\Event::find($id);
        $event->delete();
        return redirect()->route('events.index')
            ->with('success','Event deleted successfully');
    }

    /**
     * Display the specified event.
     */
    public function show(string $id)
    {
        $event = \App\Models\Event::findOrFail($id);
        return view('events.show', ['event' => $event]);
    }


    public function publish()
    {

    }

    public function search()
    {
        
    }
}
