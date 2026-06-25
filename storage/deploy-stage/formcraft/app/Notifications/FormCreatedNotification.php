<?php

namespace App\Notifications;

use App\Models\Form;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FormCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Form $form,
        public User $creator,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'form_created',
            'form_id' => $this->form->id,
            'form_title' => $this->form->title,
            'creator_name' => $this->creator->name,
            'creator_role' => $this->creator->roleLabel(),
        ];
    }
}
