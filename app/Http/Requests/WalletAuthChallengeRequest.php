<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletAuthChallengeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'app_name' => $this->header('X-App-Name'),
            'version' => $this->header('X-App-Version'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'app_name' => ['required', 'in:peppool-wallet'],
            'address' => ['required', 'string', 'max:64'],
            'version' => ['required', 'string', 'regex:/^\d{1,3}\.\d{1,3}\.\d{1,3}$/'],
        ];
    }
}
