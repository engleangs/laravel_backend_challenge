<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;
use App\Mail\SendEmailOrder;
use App\Models\Order;
use App\Models\Shipping;
use App\Models\Customer;
class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;
    protected $order_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $order_id)
    {
        //
        $this->order_id = $order_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->details = Order::getOrderResult( $this->order_id);
        $email = new SendEmailOrder( $this->details );
        Mail::to( $this->details['email'] )
        ->send( $email );
    }
}
