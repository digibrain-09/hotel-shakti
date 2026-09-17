<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;
    public $mailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($formData)
    {
        $this->mailData = $formData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->mailData['subject'];
		$replyToName = $this->mailData['full_name'];
		$replyTo = $this->mailData['email'];
		
		return $this->view('emails.send_mail')->cc($replyTo, $replyToName)->subject($subject)->replyTo($replyTo, $replyToName)->with(['data' => $this->mailData]);
    }
}
