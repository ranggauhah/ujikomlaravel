<?php

namespace App\Observers;

use App\Models\LogAktivitas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BaseObserver
{
    /**
     * Log activity
     */
    protected function logActivity(string $aksi, Model $model, ?array $dataLama = null)
    {
        if (!Auth::check()) {
            return;
        }

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aksi' => $aksi,
            'tabel' => $model->getTable(),
            'data_lama' => $dataLama ? json_encode($dataLama) : null,
            'data_baru' => json_encode($model->toArray()),
        ]);
    }

    /**
     * Handle the "created" event.
     */
    public function created(Model $model): void
    {
        $this->logActivity('create', $model);
    }

    /**
     * Handle the "updated" event.
     */
    public function updated(Model $model): void
    {
        $this->logActivity('update', $model, $model->getOriginal());
    }

    /**
     * Handle the "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->logActivity('delete', $model, $model->toArray());
    }
}
