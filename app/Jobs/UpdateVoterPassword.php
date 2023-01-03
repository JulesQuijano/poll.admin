<?php

namespace App\Jobs;

use App\Models\Voter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;

class UpdateVoterPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $voter;
    protected $raw;

    public $tries = 3;

    public function backoff()
    {
        return [2, 5, 10];
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Voter $voter, $raw)
    {
        $this->voter=$voter;
        $this->raw=$raw;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->voter->has_password_request=false;
        $this->voter->password=Hash::make($this->raw);
        $this->voter->save();
    }
}
