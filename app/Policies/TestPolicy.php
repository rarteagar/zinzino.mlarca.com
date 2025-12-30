<?php

namespace App\Policies;

use App\Models\Test;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TestPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Test $test)
    {
        // User who entered the test
        if ($test->entered_by_id === $user->id) {
            return true;
        }

        // If the test belongs to a client of the user
        if ($test->client && $test->client->user_id === $user->id) {
            return true;
        }

        // If the test subject is the user
        if ($test->subject_user_id && $test->subject_user_id === $user->id) {
            return true;
        }

        return false;
    }
}
