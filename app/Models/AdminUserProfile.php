<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminUserProfile extends Model
{
    protected $table = 'admin_user_profile';

    protected $fillable = ['admin_user_id', 'name', 'phone', 'avatar'];

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }
}
