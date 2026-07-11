<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('admin')->first();
        $doctor = User::role('doctor')->first();
        $reception = User::role('reception')->first();

        if (! $admin || ! $doctor) return;

        $conversation = Conversation::create([
            'created_by' => $admin->id,
            'type' => 'private',
        ]);
        $conversation->users()->attach([$admin->id, $doctor->id]);

        $messages = [
            ['user_id' => $admin->id, 'body' => 'Habari daktari, tunahitaji kuhudumia wagonjwa wengi leo.'],
            ['user_id' => $doctor->id, 'body' => 'Sawa, niko tayari. Reception itawapa priority.'],
            ['user_id' => $admin->id, 'body' => 'Vizuri, asante.'],
        ];

        foreach ($messages as $msg) {
            Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => $msg['user_id'],
                'body' => $msg['body'],
                'type' => 'text',
            ]);
        }

        $conversation->touch();
    }
}
