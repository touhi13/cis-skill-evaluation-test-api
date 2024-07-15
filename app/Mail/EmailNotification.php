<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @param array $details
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->subject($this->details['subject'] ?? '')
            ->to($this->details['to'] ?? '')
            ->cc($this->details['cc'] ?? '')
            ->bcc($this->details['bcc'] ?? '')
            ->html($this->details['email_body'] ?? '');

        if (isset($this->details['attachments']) && is_array($this->details['attachments'])) {
            foreach ($this->details['attachments'] as $attachment) {
                if (isset($attachment['path'], $attachment['name'], $attachment['mime'])) {
                    $email->attach($attachment['path'], [
                        'as'   => $attachment['name'],
                        'mime' => $attachment['mime'],
                    ]);
                }
            }
        }

        return $email;
    }
}
