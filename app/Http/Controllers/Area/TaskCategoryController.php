<?php

namespace App\Http\Controllers\Area;

use App\Models\TaskCategory;
use Illuminate\Http\Request;

class TaskCategoryController extends BaseController
{
    public function index()
    {
        $data = [];
        $this->loadThemePreferences($data);
        $data['title'] = 'Kategori Task';
        $data['page'] = 'task-category';
        $data['categories'] = TaskCategory::orderBy('nama')->get();

        return view('area.task_category', $data);
    }

    public function add(Request $request)
    {
        $validated = $request->validate(['nama' => 'required|string|max:100']);
        TaskCategory::create(['nama' => $validated['nama'], 'tipe' => 'general', 'is_system' => false]);

        return response()->json(['status' => 1, 'msg' => 'Berhasil menambahkan kategori']);
    }

    public function edit(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:task_category,id',
            'nama' => 'required|string|max:100',
        ]);
        TaskCategory::whereKey($validated['id'])->update(['nama' => $validated['nama']]);

        return response()->json(['status' => 1, 'msg' => 'Berhasil memperbarui kategori']);
    }

    public function delete(Request $request)
    {
        $category = TaskCategory::findOrFail($request->id);
        if ($category->is_system) {
            return response()->json(['status' => 0, 'msg' => 'Kategori bawaan sistem tidak dapat dihapus'], 422);
        }
        if ($category->tasks()->exists()) {
            return response()->json(['status' => 0, 'msg' => 'Kategori yang masih digunakan tidak dapat dihapus'], 422);
        }
        $category->delete();

        return response()->json(['status' => 1, 'msg' => 'Berhasil menghapus kategori']);
    }
}
