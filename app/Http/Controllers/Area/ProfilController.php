<?php

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfilController extends BaseController
{
    public function index()
    {
        $data = [];
        $this->loadThemePreferences($data);
        $data['title'] = 'Profil';
        $data['page'] = 'profil';

        return view('area.profil', $data);
    }

    public function edit(Request $request)
    {
        request()->validate([
            'fullname' => 'required',
            'username' => 'required|unique:users,username,' . Auth::user()->id . ',id',
            'handphone' => 'required',
            'jk' => 'required',
            'asal' => 'required',
            'img' => 'image|mimes:jpg,png,jpeg|max:8192',
            'email' => 'required|email|unique:users,email,' . Auth::user()->id . ',id',
        ]);

        if ($request->has('password') && $request->password != '' && $request->password != null) {
            $data = $request->all();
            $data['password'] = Hash::make($request->password);
        } else {
            $data = $request->except('password');
        }

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $file_name = 'img_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();

            try {
                $file->storeAs('user', $file_name, 'public');
                $data['img'] = 'storage/user/' . $file_name;
            } catch (\Exception $e) {
                $result['status'] = '0';
                $result['msg'] = 'Gagal mengupload foto';
                return response()->json($result);
            }
        }



        try {
            DB::beginTransaction();
            User::where('id', Auth::user()->id)->update($data);
            DB::commit();

            $response['status'] = 1;
            $response['msg'] = 'Berhasil perbarui data';
        } catch (Exception $e) {
            DB::rollBack();

            $response['status'] = 0;
            $response['msg'] = "Gagal perbarui data!!";
            $response['error'] = $e->getMessage();
        }

        return response()->json($response);
    }
}
