<?php

declare(strict_types=1);

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Own profile only; there is no id in the request to point at someone else.
 */
final class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // NIP is 18 digits; teachers who are not civil servants have none.
            'nip' => ['nullable', 'string', 'regex:/^\d{18}$/'],
        ];
    }
}
