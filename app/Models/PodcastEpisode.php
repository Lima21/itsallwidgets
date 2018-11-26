<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Spatie\Feed\Feedable;
//use Spatie\Feed\FeedItem;
use Illuminate\Notifications\Notifiable;

//class PodcastEpisode extends Model implements Feedable
class PodcastEpisode extends Model
{
    use Notifiable;

    protected $fillable = [
        'title',
        'email',
        'short_description',
        'long_description',
        'episode',
        'avatar_url',
        'reddit_url',
        'website_url',
        'github_url',
        'twitter_url',
        'app_url',
        'is_visible',
        'is_uploaded',
        'private_notes',
    ];

    protected $hidden = [
        'email',
        'private_notes',
        'is_visible',
    ];

    public function scopeVisible($query)
    {
        $query->where('is_visible', '=', true);
    }

    public function listDescription()
    {
        return $this->is_uploaded ? $this->short_description : 'Coming soon';
    }

    public function url()
    {
        return url('podcast/' . ($this->episode ?: '0') . '/' . str_slug($this->title));
    }

    public function adminUrl()
    {
        return url('podcast_admin/' . $this->id);
    }

}
