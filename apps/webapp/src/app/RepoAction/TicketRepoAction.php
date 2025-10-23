<?php

namespace App\RepoAction;

use Illuminate\Support\Facades\DB;

class TicketRepoAction
{
    public static function crear($ticket)
    {
        return DB::table('tickets')->insertGetId($ticket);
    }

    public static function actualizar($id, $ticket)
    {
        return DB::table('tickets')->where('ticket_id', $id)->update($ticket);
    }
}
