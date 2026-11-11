<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePengembalianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Peminjam hanya wajib mengisi peminjaman_id & tanggal_kembali.
     * kondisi_alat & denda TIDAK diisi peminjam — petugas yang verifikasi lewat PUT.
     */
    public function rules(): array
    {
        return [
            'peminjaman_id'   => 'required|exists:peminjaman,id',
            'tanggal_kembali' => 'required|date',
            'keterangan'      => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'peminjaman_id.required'   => 'Peminjaman wajib dipilih',
            'peminjaman_id.exists'     => 'Peminjaman tidak ditemukan',
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi',
            'tanggal_kembali.date'     => 'Format tanggal tidak valid',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
