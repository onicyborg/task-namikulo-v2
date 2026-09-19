<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $table = 'user_preferences';
    protected $fillable = [
        'user_id',
        'layout',
        'sidebar_color',
        'theme_color',
        'mini_sidebar',
        'sticky_header',
    ];
    protected $casts = [
        'mini_sidebar' => 'boolean',
        'sticky_header' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toArray(): array
    {
        return [
            'layout' => $this->layout,
            'sidebar' => $this->sidebar_color,
            'color' => $this->theme_color,
            'miniSidebar' => $this->mini_sidebar,
            'stickyHeader' => $this->sticky_header,
        ];
    }
}
