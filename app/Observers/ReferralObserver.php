<?php

namespace App\Observers;

use App\Models\Referral;
use App\Models\User;
use App\Notifications\ReferralCreatedNotification;
use Illuminate\Support\Facades\Notification;

class ReferralObserver
{
    /**
     * Notify the counseling center when a new referral is raised.
     */
    public function created(Referral $referral): void
    {
        $notification = new ReferralCreatedNotification($referral);

        $counselors = User::query()
            ->where('role', 'counselor')
            ->when($referral->referred_by_id, fn ($query, $id) => $query->where('id', '!=', $id))
            ->get();

        if ($counselors->isNotEmpty()) {
            Notification::send($counselors, $notification);
        }

        if ($email = config('referrals.center_email')) {
            Notification::route('mail', $email)->notify($notification);
        }
    }
}
