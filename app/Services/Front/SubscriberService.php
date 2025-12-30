<?php

namespace App\Services\Front;

use App\Models\Subscriber;

class SubscriberService
{
    public function addSubscriber(string $email): array
    {
        $email = trim(strtolower($email));

        $existing = Subscriber::where('email', $email)->first();

        if($existing){
            if($existing->status == 1){
                return [
                    'status' => false,
                    'message' => 'You are already subscribed with this email.',
                ];
            }

            $existing->update([
                'status' => 1,
            ]);

            return [
                'status' => true,
                'message' => 'You subscription is active.',
            ];
        }

        Subscriber::create([
            'email' => $email,
            'status' => 1,
        ]);

        return [
            'status' => true,
            'message' => 'Thank you for subscribing.',
        ];  
    }
}
