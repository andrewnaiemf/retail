<?php

namespace App\Notifications;

use Twilio\Rest\Client;

class WhatsappNotification
{

    public static function sendWhatsAppMessage($message, $recipientNumber)
    {
        $twilioSid = config('app.twilio_sid');
        $twilioToken = config('app.twilio_auth_token');
        $twilioWhatsAppNumber = 'whatsapp:+966541138239';
        $recipientNumber = 'whatsapp:'. $recipientNumber;
        $messageBody = $message;

        $twilio = new Client($twilioSid, $twilioToken);

        try {
            $message = $twilio->messages
                ->create(
                $recipientNumber,
                [
                    "from" => $twilioWhatsAppNumber,
                    "body" => $messageBody,
                ]
            );

            return response()->json(['message' => 'WhatsApp message sent successfully']);
        } catch (\Exception $e) {dd($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
