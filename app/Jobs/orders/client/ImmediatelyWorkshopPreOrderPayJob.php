<?php

namespace App\Jobs\orders\client;

use App\Models\Preorder;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImmediatelyWorkshopPreOrderPayJob implements ShouldQueue
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
     */
    public function handle(): void
    {
        $fields = $this->data;
        try {
            $order = Preorder::where('ticket_id', $fields["id"])->first();
            $user = User::where('email', $fields["client_email"])->first();
            $workshop = User::where('email', $fields["workshop_email"])->first();

            $preAmountUser = $user->wallet->amount;
            $preAmountWorkshop = $workshop->wallet->amount;

            $user->wallet->amount -= $fields["price"];
            $workshop->wallet->amount += $fields["price"];

            $user->wallet->save();
            $workshop->wallet->save();

            $newAmountUser = $user->wallet->amount;
            $newAmountWorkshop = $workshop->wallet->amount;

            $user->wallet->charges()->create([
                'charge' => $fields["price"],
                'pre_mount' => $preAmountUser,
                'new_amount' => $newAmountUser,
                'type' => 1,
            ]);
            $workshop->wallet->charges()->create([
                'charge' => $fields["price"],
                'pre_mount' => $preAmountWorkshop,
                'new_amount' => $newAmountWorkshop,
            ]);

            $order->delete();
        } catch (\Exception $exception) {
            echo $exception;
        }
    }
}
