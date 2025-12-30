<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'url',
        'icon',
        'order',
        'parent_id',
        'permission',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function hasChildren(): bool
    {
        return $this->children->isNotEmpty();
    }

    public function isActive(): bool
    {
        return request()->is(trim($this->url, '/'));
    }
}
