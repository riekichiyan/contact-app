<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'last_name'   => ['required', 'string', 'max:8'],
            'first_name'  => ['required', 'string', 'max:8'],
            'gender'      => ['required', 'in:1,2,3'],
            'email'       => ['required', 'email', 'max:255'],
            'tel'         => ['required', 'digits_between:10,11'],
            'address'     => ['required', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'detail'      => ['required', 'string', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            // 名前
            'last_name.required'  => '姓を入力してください。',
            'last_name.max'       => '姓は8文字以内で入力してください。',
            'first_name.required' => '名を入力してください。',
            'first_name.max'      => '名は8文字以内で入力してください。',

            // 性別
            'gender.required' => '性別を選択してください。',
            'gender.in'       => '正しい性別を選択してください。',

            // メール
            'email.required' => 'メールアドレスを入力してください。',
            'email.email'    => 'メールアドレスの形式が正しくありません。',
            'email.max'      => 'メールアドレスは255文字以内で入力してください。',

            // 電話番号
            'tel.required'        => '電話番号を入力してください。',
            'tel.digits_between'  => '電話番号は10桁または11桁の数字で入力してください。',

            // 住所
            'address.required' => '住所を入力してください。',
            'address.max'      => '住所は255文字以内で入力してください。',

            // カテゴリ
            'category_id.required' => 'お問い合わせ種別を選択してください。',
            'category_id.exists'   => '正しいお問い合わせ種別を選択してください。',

            // 内容
            'detail.required' => 'お問い合わせ内容を入力してください。',
            'detail.max'      => 'お問い合わせ内容は120文字以内で入力してください。',
        ];
    }
}