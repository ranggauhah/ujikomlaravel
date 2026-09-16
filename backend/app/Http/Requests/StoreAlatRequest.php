<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAlatRequest extends FormRequest
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
            'kategori_id' => 'required|exists:kategori_alat,id',
            'nama_alat'   => 'required|string|max:255',
            'merk'        => 'nullable|string|max:255',
            'jumlah'      => 'required|integer|min:0',
            'deskripsi'   => 'nullable|string',
            'kondisi'     => 'required|in:baik,rusak,dalam_perbaikan',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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
            'kategori_id.required' => 'Kategori wajib dipilih',
            'kategori_id.exists' => 'Kategori tidak ditemukan',
            'nama_alat.required' => 'Nama alat wajib diisi',
            'jumlah.required' => 'Jumlah wajib diisi',
            'jumlah.integer' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah minimal 0',
            'kondisi.in' => 'Kondisi harus: baik, rusak, atau dalam_perbaikan',
            'foto.image'  => 'File harus berupa gambar',
            'foto.mimes'  => 'Format gambar harus jpeg, png, atau jpg',
            'foto.max'    => 'Ukuran gambar maksimal 2MB',
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
