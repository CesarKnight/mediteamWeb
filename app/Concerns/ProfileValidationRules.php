<?php

namespace App\Concerns;

use App\Enums\UsuarioTipo;
use App\Models\User;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function profileRules(?int $userId = null): array
    {
        return [
            'name' => $this->nameRules(),
            'lastName' => $this->nameRules(),
            'ci' => $this->ciRules(),
            'fechaNacimiento' => ['required', 'date'],
            'telefono' => ['string', 'max:8'],
            'tipo' => ['required', Rule::enum(UsuarioTipo::class)],
            'email' => $this->emailRules($userId),
        ];
    }

    /**
     * Get the validation rules used to validate carnets de indentidad.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function ciRules(): array
    {
        return ['required', 'string', 'max:10'];
    }

    /**
     * Get the validation rules used to validate user names.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * Get the validation rules used to validate user emails.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function emailRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            $userId === null
                ? Rule::unique(User::class)
                : Rule::unique(User::class)->ignore($userId),
        ];
    }
}
