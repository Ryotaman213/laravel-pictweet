<?php

namespace App\Http\Requests;

use App\Rules\MaxTextBytes;
use Illuminate\Foundation\Http\FormRequest;

abstract class TweetFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'max:65535', new MaxTextBytes],
            'image' => ['nullable', 'string', 'url:http,https', 'max:2048'],
        ];
    }
}
