<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FileSentNotification extends Notification
{
    use Queueable;

    protected $fileModel;

    public function __construct($fileModel)
    {
        $this->fileModel = $fileModel;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Nouveau fichier envoyé dans votre groupe')
                    ->line('Un nouveau fichier a été ajouté au groupe.')
                    ->action('Voir le fichier', url('/files/' . $this->fileModel->file_name))
                    ->line('Merci d\'utiliser notre application !');
    }

    public function toArray($notifiable)
    {
        return [
            'file_id' => $this->fileModel->id,
            'file_name' => $this->fileModel->file_name,
            'groupe_id' => $this->fileModel->groupe_id,
        ];
    }
}
