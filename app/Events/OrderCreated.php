<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Log;

class OrderCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $order; // Changed to hold the order object

    /**
     * Create a new event instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }
    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new Channel('orders');
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith()
    {
        Log::info('Broadcasting order created event with order: ' . $this->order->order_number);  // Log the order details
        
        return [
            'order_id' => $this->order->order_number,
            'id' => $this->order->id,
            'customer_name' => $this->order->customer_name,
            'phone_number' => $this->order->phone_number,
            'phone_number' => $this->order->phone_number,
            //'arrived_from' => $this->order->created_by,
          //  'message' => 'Nouvelle commande créée: #' . $this->order->order_number,
        ];
    }

    public function broadcastAs()
    {
        return 'OrderCreated';
    }
}
