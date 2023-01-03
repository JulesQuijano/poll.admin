<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;

class UpdateAdminPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $password;

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
    public function __construct(User $user, $password)
    {
        $this->user=$user;
        $this->password=$password;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->password = Hash::make($this->password);
        $this->user->save();
    }
}
