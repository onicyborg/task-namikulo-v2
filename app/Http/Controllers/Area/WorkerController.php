<?php

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class WorkerController extends BaseController
{
    public function index()
    {
        $data = [];
        $this->loadThemePreferences($data);
        $data['title'] = 'Data Worker';
        $data['page'] = 'worker';
        return view('area.worker', $data);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('term');

        $data = User::where('fullname', 'like', '%' . $searchTerm . '%')->where('role', 'Worker')->get();

        $results = [];
        if ($request->has('empty_result') && $request->empty_result == 'true') {
            array_unshift($results, [
                'id' => 'all',
                'text' => 'Seluruh Worker'
            ]);
        }
        foreach ($data as $item) {
            $results[] = [
                'id' => $item->id,
                'text' => $item->fullname
            ];
        }

        return response()->json($results);
    }

    public function listActive(Request $request)
    {
        $worker = User::where('role', 'Worker');

        if (!$request->has('order')) {
            $worker->orderBy('created_at', 'desc');
        }

        $datatable = DataTables::eloquent($worker)
            ->addIndexColumn();

        return $datatable->make(true);
    }

    public function listDelete(Request $request)
    {
        $worker = User::where('role', 'Worker')->onlyTrashed();

        if (!$request->has('order')) {
            $worker->orderBy('created_at', 'desc');
        }

        $datatable = DataTables::eloquent($worker)
            ->addIndexColumn();

        return $datatable->make(true);
    }

    public function add(Request $request)
    {
        request()->validate([
            'fullname' => 'required',
            'username' => 'required|unique:users,username',
            'handphone' => 'required',
            'jk' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'hex' => 'required',
        ]);

        $data = $request->all();
        $data['role'] = 'Worker';
        $data['password'] = Hash::make($request->password);
        $data['img'] = 'files/img/user.png';

        try {
            DB::beginTransaction();
            User::create($data);
            DB::commit();

            $response['status'] = '1';
            $response['msg'] = 'Berhasil menambahkan data';
        } catch (Exception $e) {
            DB::rollBack();

            $response['status'] = '0';
            $response['msg'] = "Gagal menambahkan data!!";
            $response['error'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function edit(Request $request)
    {
        request()->validate([
            'fullname' => 'required',
            'username' => 'required|unique:users,username,' . $request->id . ',id',
            'handphone' => 'required',
            'jk' => 'required',
            'hex' => 'required',
            'email' => 'required|email|unique:users,email,' . $request->id . ',id',
        ]);


        if ($request->has('password') && $request->password != '' && $request->password != null) {
            $data = $request->all();
            $data['password'] = Hash::make($request->password);
        } else {
            $data = $request->except('password');
        }


        try {
            DB::beginTransaction();
            User::where('id', $request->id)->update($data);
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

    public function detail($id)
    {
        $worker = User::find($id);
        if (request()->ajax()) {
            if ($worker != null) {
                $response['status'] = 1;
                $response['worker'] = $worker;
                $response['msg'] = "Data ditemukan";
            } else {
                $response['status'] = 0;
                $response['msg'] = "Data tidak ditemukan!!";
            }
        } else {
            if ($worker != null) {
                $worker->total = Task::where('worker_id', $id)->count();
                $worker->pay = Task::where('worker_id', $id)->sum('pay_worker');
                $worker->margin = Task::where('worker_id', $id)->sum('margin');

            $data = [];
                $this->loadThemePreferences($data);
                $data['title'] = 'Data worker';
                $data['page'] = 'worker';
                $data['worker'] = $worker;
                return view('area.worker_detail', $data);
            } else {
                abort(404);
            }
        }

        return response()->json($response);
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();
            User::where('id', $request->id)->delete();
            DB::commit();

            $response['status'] = 1;
            $response['msg'] = 'Berhasil menghapus data';
        } catch (Exception $e) {
            DB::rollBack();

            $response['status'] = 0;
            $response['msg'] = "Gagal menghapus data!!";
            $response['error'] = $e->getMessage();
        }
        return response()->json($response);
    }
}
