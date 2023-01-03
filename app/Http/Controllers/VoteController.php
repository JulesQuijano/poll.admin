<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\Position;
use App\Models\Vote;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VoteController extends Controller
{
    public function index(Poll $poll)
    {
        $count = $poll->votes->count();

        $votes = Vote::query()
            ->where('poll_id',$poll->id)
            ->when(\Illuminate\Support\Facades\Request::input('search'), function ($query, $search) {
                $query->where('student_number','like',"%{$search}%");
            })
            ->orderBy('id')
            ->cursorPaginate(10)
            ->withQueryString()
            ->through(fn($vote) => [
                'id' => $vote->id,
                'nominee'=> $vote->nominee->name,
                'position' => Position::find($vote->position_id)->name,
                'student_number' => $vote->student_number,
                'college' => $vote->college,
                ]);

        return Inertia::render('Votes/Index', [
            'poll'=>$poll,
            'votes'=>$votes,
            'totalCount'=>$count,
            'filters'=>\Illuminate\Support\Facades\Request::only(['search'])
        ]);
    }
}
