<?php

namespace App\Notifications;

use Twilio\Rest\Client;

class WhatsappNotification
{

    public function sendWhatsAppMessage()
    {
        $twilioSid = config('app.twilio_sid');
        $twilioToken = config('app.twilio_auth_token');
        $twilioWhatsAppNumber = 'whatsapp:+14155238886';
        $recipientNumber = 'whatsapp:+201274696869';
        $message = "Hello from Twilio 🚀";

        $twilio = new Client($twilioSid, $twilioToken);

        try {
            $twilio->messages->create(
                $recipientNumber,
                [
                    "from" => $twilioWhatsAppNumber,
                    "body" => $message,
                ]
            );

            return response()->json(['message' => 'WhatsApp message sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
