<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Position;
use App\Models\Vote;
use App\Models\Poll;
use App\Models\Voter;

class VoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $polls = Poll::all();
        $voters = Voter::all();
        $positions = Position::all();

        foreach ($polls as $poll)
        {
            foreach ($voters as $voter)
            {
                foreach ($positions as $position) {
                    $nominees = $poll->nominees->where('position_id', $position->id);
                    Vote::create([
                        'poll_id' => $poll->id,
                        'nominee_id' => $nominees->random()->id,
                        'position_id' => $position->id,
                        'student_number' => $voter->student_number,
                        'college' => $voter->college,
                    ]);
                }
            }
        }
    }
}
