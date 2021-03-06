<?php

namespace Demo\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $ref=null)
    {
        $ref = $ref ? urldecode($ref) : env('APP_URL');
        $url = parse_url($ref);
        $url['query'] = implode('&', array_filter([array_get($url, 'query', ''), "token=$token"]));
        $this->url = array_get($url, 'scheme', 'http').'://'.array_get($url, 'host', '').array_get($url, 'path', '').'?'.array_get($url, 'query').'#'.array_get($url, 'fragment');
        $this->queue = 'email';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.resetPasswordEmail');
    }
}
