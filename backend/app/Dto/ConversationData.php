<?php

namespace App\Dto;

class ConversationData
{
    public function __construct(
        public string $user_name,
        public string $user_message,
        public string $ai_response,
        public ?string $api_response = null
    ) {}
}
