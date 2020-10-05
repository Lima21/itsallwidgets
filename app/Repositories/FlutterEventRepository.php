<?php

namespace app\Repositories;

use DB;
use App\Models\FlutterEvent;

class FlutterEventRepository
{
    public function getAll()
    {
        return FlutterEvent::orderBy('id', 'desc');
    }

    /**
     * Find a app by it slug
     *
     * @param string $slug
     * @return void
     */
    public function getBySlug($slug)
    {
        return FlutterEvent::where('slug', $slug)->firstOrFail();
    }

    /**
     * Find a app by it primary key
     *
     * @param integer $id
     * @return Collection
     */
    public function getById($id)
    {
        return FlutterEvent::findOrFail($id);
    }

    public function findRandomOnline()
    {
        $events = FlutterEvent::approved()
                        ->future()
                        ->where('is_online', '=', true)
                        ->get();

        $count = $events->count();

        if ($count == 0) {
            return false;
        }

        $random = rand(0, $count - 1);

        return $events[$random];
    }

    public function findByCoordinates($latitude, $longitude)
    {
        $latitude = floatval($latitude);
        $longitude = floatval($longitude);

        if (!$latitude || !$longitude) {
            return false;
        }

        // https://coderwall.com/p/zetcpw/calculating-distance-between-two-points-latitude-longitude
        $events = DB::select("SELECT id,
            ( 6371 * acos( cos( radians($latitude) )
            * cos( radians( latitude ) )
            * cos( radians( longitude ) - radians($longitude) ) + sin( radians($latitude) )
            * sin( radians( latitude ) ) ) )
            AS calculated_distance
            FROM flutter_events
            WHERE archived_at IS NULL
            AND is_approved = 1
            AND is_visible = 1
            AND CURDATE() <= event_date
            HAVING calculated_distance <= 1000
            ORDER BY calculated_distance, event_date
            LIMIT 1");

        if (!count($events)) {
            return false;
        }

        return $this->getById($events[0]->id);
    }

    /**
     * Create a new app.
     *
     * @param FlutterEvent $app
     */
    public function store($input, $user_id)
    {
        $event = new FlutterEvent;
        $event->fill($input);
        $event->user_id = $user_id;

        $url = $event->event_url;
        $url = explode('?', $url)[0];
        $url = rtrim($url, '/');
        $event->event_url = $url;

        $event->save();

        return $event;
    }

    /**
     * Update a app.
     *
     * @param FlutterEvent $app
     */
    public function update($event, $input)
    {
        $event->fill($input);

        $url = $event->event_url;
        $url = explode('?', $url)[0];
        $url = rtrim($url, '/');
        $event->event_url = $url;

        $event->save();

        return $event;
    }

    /**
     * Show a specified app.
     *
     * @param FlutterEvent $slug
     */
    public function show($slug)
    {
        return FlutterEvent::where('slug', $slug)->firstOrFail();
    }

    /**
     * Approve a specified app
     *
     * @param integer $id
     * @return Collection
     */
    public function approve($id)
    {
        return FlutterEvent::where('id', $id)->update(['is_approved' => 1]);
    }

    /**
     * Delete a specified app
     *
     * @param  FlutterApp $app
     * @return
     */
    public function delete($app)
    {
        return FlutterEvent::destroy($app);
    }
}
