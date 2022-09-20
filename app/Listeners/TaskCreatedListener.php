<?php

namespace App\Listeners;
use App\Events\TaskCreate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contract\Mail\Mailer;

class TaskCreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ExampleEvent  $event
     * @return void
     */
    public function handle(TaskCreate $event)
    {
        $user = $event->user;
        $task = $event->task;


        Mail::send('emails.taskCreated', ['user' => $user], function($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Urgent::Task Created for you');
            $message->from('yourDashboard@gmail.com', 'react-app');
        });
        
    }
}
