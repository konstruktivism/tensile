<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;
use Spatie\Mjml\Exceptions\CouldNotConvertMjml;
use Spatie\Mjml\Mjml;

class MagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @throws CouldNotConvertMjml
     */
    public function build()
    {
        // Read the MJML template content
        $mjmlContent = view('mail.magic-link', [
            'url' => $this->url,
        ])->render();

        $htmlContent = Mjml::new()->convert($mjmlContent)->html();

        // Return the rendered HTML
        return $this->view('mail.raw', ['htmlContent' => $htmlContent])
            ->subject(__('Login via email'));
    }
}
