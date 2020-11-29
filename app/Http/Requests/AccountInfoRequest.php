<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Restrict the fields that the user can change.
     *
     * @return array
     */
    public function validationData()
    {
        return $this->only(backpack_authentication_column(), 'first_name','middle_name', 'last_name', 'contact_number');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = backpack_auth()->user();
        $userId = $user->id;
        return [
            backpack_authentication_column() => [
                'required',
                backpack_authentication_column() == 'email' ? 'email' : '',
                Rule::unique($user->getTable())->ignore($user->getKey(), $user->getKeyName()),
            ],
            'contact_number'     => 'required|unique:'.config('permission.table_names.users', 'users').',contact_number,'. $userId,
            // 'contact_number' => ['required', 'contact_number', Rule::unique($user->getTable())->ignore($user->getKey(), $user->getKeyName())],//'required|unique:'.config('permission.table_names.users', 'users').',contact_number',
            'first_name' => 'required',
            'last_name' => 'required',
        ];
    }
}
