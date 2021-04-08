<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountCreated extends Mailable
{
  use Queueable, SerializesModels;

  private $email;
  private $token;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($email, $token)
  {
    $this->email = $email;
    $this->token = $token;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->view('emails.account_created')
      ->subject("Création d'un compte Washpoint")
      ->from(env('MAIL_FROM_ADDRESS'))
      ->with([
        'email' => $this->email,
        'token' => $this->token,
        'signature' => env('MAIL_SIGNATURE')
      ]);
  }
}
