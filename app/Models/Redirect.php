<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     * Only track created_at, not updated_at.
     *
     * @var bool
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'old_path',
        'new_path',
        'status_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status_code' => 'integer',
    ];

    /**
     * Check if this is a permanent redirect.
     */
    public function isPermanent(): bool
    {
        return $this->status_code === 301;
    }

    /**
     * Check if this is a temporary redirect.
     */
    public function isTemporary(): bool
    {
        return $this->status_code === 302;
    }
}
