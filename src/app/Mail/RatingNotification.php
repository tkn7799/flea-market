<?php

namespace App\Mail;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RatingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $purchase;
    public $fromUser;

    /**
     * @param Purchase $purchase 取引情報
     * @param User $fromUser 評価を付けたユーザー
     */
    public function __construct(Purchase $purchase, User $fromUser)
    {
        $this->purchase = $purchase;
        $this->fromUser = $fromUser;
    }

    /**
     * メールの構築
     */
    public function build()
    {
        return $this->subject('評価が届きました')
                    ->view('emails.rating_notification');
    }
}
