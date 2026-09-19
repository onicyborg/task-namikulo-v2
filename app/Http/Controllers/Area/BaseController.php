<?php

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    protected function loadThemePreferences(array &$data): void
    {
        if (!Auth::check()) {
            return;
        }

        $pref = UserPreference::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'layout' => 'light',
                'sidebar_color' => 'dark',
                'theme_color' => 'blue',
                'mini_sidebar' => false,
                'sticky_header' => true,
            ]
        );

        $data['layout'] = $pref->layout;
        $data['sidebarColor'] = $pref->sidebar_color === 'light' ? 'light-sidebar' : 'dark-sidebar';
        $data['themeColor'] = $pref->theme_color;
        $data['miniSidebar'] = $pref->mini_sidebar;
        $data['stickyHeader'] = $pref->sticky_header;

        // Generate dynamic CSS variable for accent RGB
        $rgbMap = [
            'blue' => '59, 130, 246',
            'cyan' => '6, 182, 212',
            'red' => '239, 68, 68',
            'purple' => '139, 92, 246',
            'orange' => '249, 115, 22',
            'green' => '16, 185, 129',
            'pink' => '236, 72, 153',
        ];
        $rgb = $rgbMap[$pref->theme_color] ?? '59, 130, 246';
        $data['_theme_css'] = "--accent-rgb: {$rgb};";
    }
}
