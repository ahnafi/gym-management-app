<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CheckoutRequest extends FormRequest
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
            'purchasable_type' => [
                'required',
                Rule::in(['membership_package', 'gym_class', 'personal_trainer_package']),
            ],
            'purchasable_id' => 'required|integer|gt:0',
            'gym_class_schedule_id' => 'nullable|integer|gt:0',
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('purchasable_type');
            $id = $this->input('purchasable_id');
            $scheduleId = $this->input('gym_class_schedule_id');

            $tableMap = [
                'membership_package' => ['table' => 'membership_packages', 'message' => 'Paket Membership yang dipilih tidak ada.'],
                'gym_class' => ['table' => 'gym_classes', 'message' => 'Kelas Gym yang dipilih tidak tersedia.'],
                'personal_trainer_package' => ['table' => 'personal_trainer_packages', 'message' => 'Paket Personal Trainer yang dipilih tidak ditemukan.'],
            ];

            if (!isset($tableMap[$type])) {
                $validator->errors()->add('purchasable_type', 'Tipe pembelian tidak valid.');
                return;
            }

            $table = $tableMap[$type]['table'];
            $message = $tableMap[$type]['message'];

            if (!DB::table($table)->where('id', $id)->exists()) {
                $validator->errors()->add('purchasable_id', $message);
            }

            // Conditionally require gym_class_schedule_id
            if ($type === 'gym_class') {
                if (empty($scheduleId)) {
                    $validator->errors()->add('gym_class_schedule_id', 'Jadwal kelas gym harus dipilih.');
                } elseif (!DB::table('gym_class_schedules')->where('id', $scheduleId)->exists()) {
                    $validator->errors()->add('gym_class_schedule_id', 'Jadwal kelas gym yang dipilih tidak ditemukan.');
                }
            }
        });
    }

}
