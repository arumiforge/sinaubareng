<?php

declare(strict_types=1);

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

/**
 * The membership is looked up among the user's own active memberships only, so
 * another user's id gives a validation error, never a switch.
 */
final class SwitchRoleRequest extends FormRequest
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
            'membership' => ['required', 'string', 'uuid'],
        ];
    }
}
