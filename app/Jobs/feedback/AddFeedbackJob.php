<?php

namespace App\Jobs\feedback;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddFeedbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            $user = User::where('email', $this->data['user_email'])->first();

            $evaluator = User::where('email', $this->data['evaluator_email'])->first();

            $user->feedbacksFrom()->create([
                'evaluator_id' => $evaluator->id,
                'feedback' => $this->data['feedback'],
            ]);

        } catch (\Exception $exception) {
            echo $exception;
        }
    }
}
