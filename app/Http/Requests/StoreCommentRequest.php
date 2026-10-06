<?php

namespace App\Http\Requests;

use App\Rules\MaxTextBytes;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tweet_id' => ['required', 'integer', 'exists:tweets,id'],
            'text' => ['required', 'string', 'max:65535', new MaxTextBytes],
        ];
    }
}
