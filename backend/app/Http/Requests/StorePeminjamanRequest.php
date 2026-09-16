<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePeminjamanRequest extends FormRequest
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
            'tanggal_pinjam' => 'required|date',
            'keperluan' => 'nullable|string',
            'detail' => 'required|array|min:1',
            'detail.*.alat_id' => 'required|exists:alat,id',
            'detail.*.jumlah_pinjam' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi',
            'tanggal_pinjam.date' => 'Format tanggal tidak valid',
            'detail.required' => 'Detail peminjaman wajib diisi',
            'detail.min' => 'Minimal 1 alat harus dipinjam',
            'detail.*.alat_id.required' => 'Alat wajib dipilih',
            'detail.*.alat_id.exists' => 'Alat tidak ditemukan',
            'detail.*.jumlah_pinjam.required' => 'Jumlah pinjam wajib diisi',
            'detail.*.jumlah_pinjam.integer' => 'Jumlah harus berupa angka',
            'detail.*.jumlah_pinjam.min' => 'Jumlah minimal 1',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
