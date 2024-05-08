<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserObserver
{
    public function updating(User $model): void
    {
        if ($model->isDirty('avatar') && ($model->getOriginal('avatar') !== null)) {
            Storage::disk('avatar')->delete($model->getOriginal('avatar'));
        }
    }
}
