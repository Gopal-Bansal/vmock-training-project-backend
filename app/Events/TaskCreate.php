<?php

namespace App\Events;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class TaskCreate extends Event  implements ShouldBroadcast
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    use SerializesModels;
    public $user;
    public $task;
    public function __construct($user, $task)
    {
        $this->user = $user;
        $this->task = $task;
    }
    public function broadcastOn()
  {
      return ['my-channel'];
  }
  


}
