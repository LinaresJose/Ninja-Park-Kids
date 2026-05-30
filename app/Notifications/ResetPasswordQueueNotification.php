<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class ResetPasswordQueueNotification extends ResetPasswordNotification implements ShouldQueue
{
    use Queueable;
}
