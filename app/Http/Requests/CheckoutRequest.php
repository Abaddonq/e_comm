<?php

namespace App\Http\Requests;

use App\Models\Address;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'address_id' => [
                'required',
                'integer',
                'exists:addresses,id',
                Rule::in(Auth::user()->addresses()->pluck('id')->toArray()),
            ],
            'payment_method' => ['required', 'string', 'in:iyzico,stripe'],
            'terms_accepted' => ['required', 'accepted'],
        ];

        if ($this->payment_method === 'iyzico') {
            $rules['card_number'] = ['required', 'string', 'min:16'];
            $rules['card_holder'] = ['required', 'string'];
            $rules['expire_month'] = ['required', 'string'];
            $rules['expire_year'] = ['required', 'string'];
            $rules['cvv'] = ['required', 'string', 'min:3', 'max:4'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'address_id.required' => 'Please select a delivery address.',
            'address_id.exists' => 'The selected address is invalid.',
            'address_id.in' => 'The selected address is invalid.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'The selected payment method is invalid.',
            'terms_accepted.required' => 'You must accept the terms and conditions.',
            'terms_accepted.accepted' => 'You must accept the terms and conditions to proceed.',
            'card_number.required' => 'Please enter your card number.',
            'card_holder.required' => 'Please enter the card holder name.',
            'expire_month.required' => 'Please select expiry month.',
            'expire_year.required' => 'Please select expiry year.',
            'cvv.required' => 'Please enter CVV.',
        ];
    }
}
