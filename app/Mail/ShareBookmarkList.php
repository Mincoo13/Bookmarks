<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShareBookmarkList extends Mailable
{
    use Queueable, SerializesModels;
    protected $sender;
    protected $url;
    protected $bookmark_list_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sender, $url, $bookmark_list_name)
    {
        $this->sender = $sender;
        $this->url = $url;
        $this->bookmark_list_name = $bookmark_list_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.sharebookmarklist')->with(['sender' => $this->sender, 'url' => $this->url, 'bookmark_list_name' => $this->bookmark_list_name]);
    }
}
