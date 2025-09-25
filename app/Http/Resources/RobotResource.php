<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RobotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => new UserResource($this->whenLoaded('user')),
            'domain' => $this->domain,
            'currency' => $this->currency,
            'shop_name' => $this->shop_name,
            'wp_consumer_key' => $this->wp_consumer_key,
            'wp_consumer_secret' => $this->wp_consumer_secret,
        ];
    }
}
