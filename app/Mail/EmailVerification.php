<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;
    public $user,$type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$type=null)
    {
        $this->user = $user;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $view = $this->type != null ? 'resend'  : 'verification' ;
        return $this->view($view,["user" => $this->user])
                    ->from('firealert.manila@gmail.com');
    }
}
