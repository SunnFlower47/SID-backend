<?php

namespace App\Observers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogObserver
{
    /**
     * Handle the Activity "creating" event.
     */
    public function creating(Activity $activity): void
    {
        // Get request data from middleware
        $request = app(Request::class);
        $activityData = $request->attributes->get('activity_log_data', []);

        if (!empty($activityData)) {
            $activity->ip_address = $activityData['ip_address'] ?? null;
            $activity->user_agent = $activityData['user_agent'] ?? null;
            $activity->url = $activityData['url'] ?? null;
            $activity->method = $activityData['method'] ?? null;
        }
    }

    /**
     * Handle the Activity "updated" event.
     */
    public function updated(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "deleted" event.
     */
    public function deleted(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "restored" event.
     */
    public function restored(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "force deleted" event.
     */
    public function forceDeleted(Activity $activity): void
    {
        //
    }
}
