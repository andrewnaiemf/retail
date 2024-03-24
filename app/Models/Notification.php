<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [//user_id user who notify the other one
        'user_id', 'notified_user_id', 'type', 'screen', 'data', 'read'
    ];

    protected $hidden =[
        'created_at',
        'updated_at',
    ];

    public function getDataAttribute($data)
    {
        $data = json_decode($data, true);

        $messageTemplateKey = data_get($data, 'message_template', '');

        if ($messageTemplateKey) {
            $sender = data_get($data, 'sender', '');
            $data['message'] = $this->generateMessage($messageTemplateKey, $sender, $data);
        }


        return $data;
    }

    protected function generateMessage($messageTemplateKey, $sender, $data)
    {
        $message = '';
        $currentLanguage = auth()->user()->locale ?? 'ar';
        app()->setLocale($currentLanguage);
        if ($messageTemplateKey == 'new_order_messages'){
            $message = $sender['name'] . __('messages.' . $messageTemplateKey, []);
        }else{
            $message = __('messages.' . $messageTemplateKey, []);
        }

        return $message;
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiedUser()
    {
        return $this->belongsTo(User::class, 'notified_user_id');
    }



}
