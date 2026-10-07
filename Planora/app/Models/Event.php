<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //    
    protected $fillable = [
        'organizer_id', 'location_id', 'title', 'description', 'price', 'max_participants', 'min_age', 'status', 'starts_at', 'ends_at', 'image_url'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'event_tag', 'event_id', 'tag_id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_participant', 'event_id', 'user_id');
    }
}
