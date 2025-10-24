<?php

namespace App\RepoAction;

use Illuminate\Support\Facades\DB;

class TicketFeedbackRepoAction
{
    public static function crear($ticketFeedback) {
        return DB::table('tickets_feedback')->insertGetId($ticketFeedback);
    }
}
