<?php

namespace App\Notifications;

use App\Models\Notification;
use App\Models\User;

class PushNotification
{

    public static function send($sender_id, $resciever_id, $data, $screen)
    {
        $reciever = User::find($resciever_id);
        $sender = User::find($sender_id);

        $friendLocale = $reciever->lng;
        app()->setLocale($friendLocale);

        $messageTemplateKey = '';

        switch ($screen) {
            case 'rating':
                $message = $sender->name . ' ' . __('messages.rating_message', ['stars' => $data->rate]);
                $messageTemplateKey = 'rating_message';
                break;

            case 'booking':
                $message = $sender->name . ' ' . __('messages.New_booking');
                $messageTemplateKey = 'New_booking';
                break;

            case 'booking_status':
                $status = $data->status;
                $statusTranslationKey = 'provider_' . $status . '_booking';
                $actionMessage = __('messages.' . $statusTranslationKey);
                $message = $sender->name . ' ' . $actionMessage;

                $messageTemplateKey = $actionMessage;
                break;
            case 'new_order':
                $message = $sender->name . ' ' . __('messages.new_order_messages');
                $messageTemplateKey = 'new_order';
                break;
            case 'Pending_order':
                $message = $sender->name . ' ' . __('messages.your_order_pending');
                $messageTemplateKey = 'your_order_pending';
                break;
            case 'Rejected_order':
                $message = $sender->name . ' ' . __('messages.your_order_Rejected');
                $messageTemplateKey = 'your_order_Rejected';
                break;
            case 'Completed_order':
                $message = $sender->name . ' ' . __('messages.your_order_Completed');
                $messageTemplateKey = 'your_order_Completed';
                break;
            default:
                $message = '';
                break;
        }

        Notification::create([
            'user_id' =>  $sender_id,
            'notified_user_id' =>  $resciever_id,
            'type' =>  $screen,
            'screen' =>  $screen,
            'data' =>json_encode( [
                'sender' => $sender,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
                'message_template' => $messageTemplateKey,
                'data' => $data
            ])
        ]);

        $placeholder = " :sender_name";
        $message = str_replace($placeholder, '', $message);

        PushNotification::send($reciever, $screen, $message, $data = null, $type = null);
    }
}
