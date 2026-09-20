<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThemePreferenceController extends Controller
{
    public function show()
    {
        $pref = UserPreference::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'layout' => 'light',
                'sidebar_color' => 'dark',
                'theme_color' => 'white',
                'mini_sidebar' => false,
                'sticky_header' => true,
            ]
        );

        return response()->json($pref);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'layout' => 'sometimes|in:light,dark',
            'sidebar' => 'sometimes|in:light,dark',
            'color' => 'sometimes|in:white,cyan,black,purple,orange,green,red',
            'miniSidebar' => 'sometimes',
            'stickyHeader' => 'sometimes',
        ]);

        $pref = UserPreference::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'layout' => 'light',
                'sidebar_color' => 'dark',
                'theme_color' => 'white',
                'mini_sidebar' => false,
                'sticky_header' => true,
            ]
        );

        if (isset($validated['layout'])) {
            $pref->layout = $validated['layout'];
        }
        if (isset($validated['sidebar'])) {
            $pref->sidebar_color = $validated['sidebar'];
        }
        if (isset($validated['color'])) {
            $pref->theme_color = $validated['color'];
        }
        if (array_key_exists('miniSidebar', $validated)) {
            $pref->mini_sidebar = filter_var($validated['miniSidebar'], FILTER_VALIDATE_BOOLEAN);
        }
        if (array_key_exists('stickyHeader', $validated)) {
            $pref->sticky_header = filter_var($validated['stickyHeader'], FILTER_VALIDATE_BOOLEAN);
        }
        $pref->save();

        return response()->json($pref);
    }
}
