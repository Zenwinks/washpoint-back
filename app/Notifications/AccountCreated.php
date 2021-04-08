<?php

namespace App\Notifications;

use App\Mail\AccountCreated as Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountCreated extends Notification
{
  use Queueable;

  private $email;
  private $token;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct($email, $token)
  {
    $this->email = $email;
    $this->token = $token;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param mixed $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param mixed $notifiable
   * @return Mailable
   */
  public function toMail($notifiable)
  {
    return (new Mailable($this->email, $this->token))->to($notifiable->email);
  }

  /**
   * Get the array representation of the notification.
   *
   * @param mixed $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      //
    ];
  }
}
