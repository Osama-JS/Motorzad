<?php

namespace App\Notifications;

use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GeneralNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public $title;
    public $messageBody;
    public $channels;
    public $actionUrl;
    public $extraData;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $messageBody
     * @param array $channels Array of channels: ['mail', 'database', 'fcm']
     * @param string|null $actionUrl
     * @param array $extraData
     */
    public function __construct($title, $messageBody, $channels = ['database'], $actionUrl = null, $extraData = [])
    {
        $this->title = $title;
        $this->messageBody = $messageBody;
        $this->channels = $channels;
        $this->actionUrl = $actionUrl;
        $this->extraData = $extraData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = [];

        if (in_array('database', $this->channels)) {
            $via[] = 'database';
            $via[] = 'broadcast';
        }

        if (in_array('mail', $this->channels) && $notifiable->email) {
            $via[] = 'mail';
        }

        if (in_array('fcm', $this->channels) && $notifiable->fcm_token) {
            $via[] = FcmChannel::class;
        }

        if (empty($via)) {
            return ['database', 'broadcast'];
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('مرحباً ' . $notifiable->name . ',')
            ->line($this->messageBody);

        if ($this->actionUrl) {
            $mail->action('عرض التفاصيل', $this->actionUrl);
        }

        return $mail->line('شكراً لاستخدامك منصة مزاد المحركات!');
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'body' => $this->messageBody,
            'action_url' => $this->actionUrl,
            'data' => $this->extraData,
        ];
    }

    /**
     * Get the FCM representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toFcm($notifiable)
    {
        return [
            'title' => $this->title,
            'body' => $this->messageBody,
            'data' => array_merge($this->extraData, [
                'action_url' => $this->actionUrl ?? '',
            ]),
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->messageBody,
            'action_url' => $this->actionUrl,
            'data' => $this->extraData,
        ]);
    }
}
