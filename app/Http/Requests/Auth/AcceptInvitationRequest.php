<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\Invitation;
use App\Models\User;
use App\Tenancy\Actions\FindInvitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * The link's token is what authorizes accepting; the controller sends an unknown,
 * used, or expired link back to the page that explains it. A new person picks a name
 * and password; someone who already has an account confirms with their password.
 */
final class AcceptInvitationRequest extends FormRequest
{
    private ?Invitation $invitation = null;

    private bool $resolved = false;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = ['token' => ['required', 'string']];

        if ($this->invitation() === null) {
            return $rules;
        }

        if ($this->invitedHasAccount()) {
            return [...$rules, 'password' => ['required', 'string']];
        }

        return [
            ...$rules,
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    public function invitation(): ?Invitation
    {
        if (! $this->resolved) {
            $this->resolved = true;
            $publicId = $this->route('invitation');
            $this->invitation = is_string($publicId)
                ? app(FindInvitation::class)->handle($publicId, $this->string('token')->toString())
                : null;
        }

        return $this->invitation;
    }

    public function invitedHasAccount(): bool
    {
        $invitation = $this->invitation();

        return $invitation !== null && User::query()->where('email', $invitation->email)->exists();
    }
}
