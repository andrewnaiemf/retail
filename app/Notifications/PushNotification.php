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

        $recieverLocale = $reciever->lng;
        app()->setLocale($recieverLocale);

        $messageTemplateKey = '';

        switch ($screen) {
            case 'Dreaft':
            case 'assignToDriver':
                $message = $sender->name . ' ' . __('messages.new_order_messages');
                $messageTemplateKey = 'new_order';
                break;
            case 'Declined':
                $message = __('messages.your_order_declined');
                $messageTemplateKey = 'your_order_declined';
                break;
            case 'Approved':
                $message = __('messages.your_order_approved');
                $messageTemplateKey = 'your_order_approved';
                break;

            case 'Received':
                $message = __('messages.your_order_received');
                $messageTemplateKey = 'your_order_received';
                break;
            case 'Processing':
                $message = __('messages.your_order_processing');
                $messageTemplateKey = 'your_order_processing';
                break;
            case 'Delivery':
                $message = __('messages.your_order_delivery');
                $messageTemplateKey = 'your_order_delivery';
                break;
            case 'Delivered':
                $message = __('messages.your_order_delivered');
                $messageTemplateKey = 'your_order_delivered';
                break;

            default:
                $message = '';
                break;
        }

        Notification::create([
            'user_id' =>  $sender_id,
            'notified_user_id' =>  $resciever_id,
            'type' =>  'Order',
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

        PushNotification::push($reciever, $screen, $message, $data = null, $type = null);
    }


    public static function push($reciever, $screen, $message, $notification_data = null, $type = null)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = env('FCM_KEY') ?? 'AAAAL0bEpHc:APA91bGpWb6PKtV-nkZK29NyvcH6ZexuIcCKKLI4LPkvvuy6eIThjI3Kog3DD_ppCg8x6gMXHD5m7AvehD10qRTzVzt8GheE0oqBYIM2Gn86Ugm3Iap7ay5PRofCpwTZu3AfaTvsuAwd';
        $devs=[];
        $devices = $reciever->device_token;
        foreach ($devices as $tokens) {
            if( is_array($tokens) ){
                foreach ($tokens as $token){
                    array_push($devs, $token);
                }
            }else{
                array_push($devs, $tokens);
            }
        }

        $data = [
            "registration_ids" =>$devs,
            "notification" => [
                "body" => $message,
                "title" => 'Drive Shild',
                "sound" => "notify.mp3",
                "tag" => "notification"
            ],
            "data" => [
                'screen' => $screen,
                'notification_data' => json_encode($notification_data),
                "body" => $message,
                "title" => 'Drive Shild',
                "type" => $type
            ]
        ];

        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

        // Execute post
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        // FCM response
        return json_decode($result);
    }

}
