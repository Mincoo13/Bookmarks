<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShareBookmark extends Mailable
{
    use Queueable, SerializesModels;
    protected $sender;
    protected $url;
    protected $bookmark_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sender, $url, $bookmark_name)
    {
        $this->sender = $sender;
        $this->url = $url;
        $this->bookmark_name = $bookmark_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.sharebookmark')->with(['sender' => $this->sender, 'url' => $this->url, 'bookmark_name' => $this->bookmark_name]);
    }
}
