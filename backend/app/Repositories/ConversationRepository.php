<?php

namespace App\Repositories;

use App\Models\Conversation;
use App\Dto\ConversationData;

class ConversationRepository
{
    public function save(ConversationData $data): Conversation
    {
        return Conversation::create([
            'user_name' => $data->user_name,
            'user_message' => $data->user_message,
            'ai_response' => $data->ai_response,
            'api_response' => $data->api_response,
        ]);
    }
    public function findByUserName(string $userName): array
    {
        return Conversation::where('user_name', $userName)
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

}
