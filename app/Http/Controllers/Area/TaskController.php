<?php

namespace App\Http\Controllers\Area;

use App\Exports\TaskExport;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TaskController extends BaseController
{
    public function index()
    {
        $data = [];
        $this->loadThemePreferences($data);
        $data['title'] = 'Data Task';
        $data['page'] = 'task';
        return view('area.task', $data);
    }

    public function list(Request $request)
    {
        $task = Task::select('task.id', 'users.fullname', 'client.customer', 'kode_task', 'task', 'order', 'deadline', 'price_order', 'pay_worker', 'margin', 'task_status', 'pay_status')
            ->leftJoin('users', 'task.worker_id', '=', 'users.id')
            ->leftJoin('client', 'task.client_id', '=', 'client.id');

        if (!$request->has('order')) {
            $task->orderBy('order', 'desc')
                ->orderBy('task.created_at', 'desc');
        }

        if ($request->has('tanggal_mulai_filter') && $request->tanggal_mulai_filter != '') {
            $task->whereDate('order', '>=', $request->tanggal_mulai_filter);
        }
        if ($request->has('tanggal_akhir_filter') && $request->tanggal_akhir_filter != '') {
            $task->whereDate('order', '<=', $request->tanggal_akhir_filter);
        }
        if ($request->has('task_status_filter') && $request->task_status_filter != '') {
            $task->where('task_status', $request->task_status_filter);
        }
        if ($request->has('pay_status_filter') && $request->pay_status_filter != '') {
            $task->where('pay_status', $request->pay_status_filter);
        }
        if ($request->has('client_id_filter') && $request->client_id_filter != '') {
            $task->where('client_id', $request->client_id_filter);
        }
        if ($request->has('worker_id_filter') && $request->worker_id_filter != '') {
            $task->where('worker_id', $request->worker_id_filter);
        }

        $datatable = DataTables::eloquent($task)
            ->editColumn('fullname', function ($task) {
                return $task->fullname ?? '-';
            })
            ->editColumn('customer', function ($task) {
                return $task->customer ?? '-';
            })
            ->editColumn('order', function ($task) {
                return hariTglIndo($task->order);
            })
            ->editColumn('deadline', function ($task) {
                return hariTglIndo($task->deadline);
            })
            ->addIndexColumn();

        return $datatable->make(true);
    }

    public function add(Request $request)
    {
        request()->validate([
            'client_id' => 'required',
            'worker_id' => 'required',
            'task' => 'required',
            'price_order' => 'required',
            'pay_worker' => 'required',
            'order' => 'required',
            'deadline' => 'required',
            'task_status' => 'required',
            'pay_status' => 'required',
        ]);

        $count = Task::withTrashed()->whereDate('created_at', date('Y-m-d'))->count();
        $kode = $count + 1;

        $data = $request->all();
        $data['kode_task'] = 'TS' . date('Ymd') . sprintf("%03d", $kode);
        $data['margin'] = $request->price_order - $request->pay_worker;

        try {
            DB::beginTransaction();
            Task::create($data);
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
            'client_id' => 'required',
            'worker_id' => 'required',
            'task' => 'required',
            'price_order' => 'required',
            'pay_worker' => 'required',
            'order' => 'required',
            'deadline' => 'required',
            'task_status' => 'required',
            'pay_status' => 'required',
        ]);

        $data = $request->all();
        $data['margin'] = $request->price_order - $request->pay_worker;

        try {
            DB::beginTransaction();
            Task::where('id', $request->id)->update($data);
            DB::commit();

            $response['status'] = '1';
            $response['msg'] = 'Berhasil perbarui data';
        } catch (Exception $e) {
            DB::rollBack();

            $response['status'] = '0';
            $response['msg'] = "Gagal perbarui data!!";
            $response['error'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function detail($id)
    {
        $task = Task::select('task.*', 'users.fullname as worker', 'client.customer')
            ->leftJoin('users', 'task.worker_id', '=', 'users.id')
            ->leftJoin('client', 'task.client_id', '=', 'client.id')
            ->find($id);
        if (request()->ajax()) {
            if ($task != null) {
                $response['status'] = 1;
                $response['msg'] = "Data ditemukan";
                $task->order_indo = hariTglIndo($task->order);
                $task->deadline_indo = hariTglIndo($task->deadline);
                $response['task'] = $task;
            } else {
                $response['status'] = 0;
                $response['msg'] = "Data tidak ditemukan!!";
            }
            return response()->json($response);
        } else {
            if ($task != null) {
                $data = [];
                $this->loadThemePreferences($data);
                $data['title'] = 'Data Task';
                $data['page'] = 'task';
                $data['task'] = $task;
                $data['detail'] = TaskDetail::where('kode_task', $task->kode_task)->get();

                return view('area.task_detail', $data);
            } else {
                abort(404);
            }
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();
            Task::where('id', $request->id)->delete();
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

    public function export(Request $request)
    {
        $data['search'] = $request->search_filter;
        $data['tanggal_mulai'] = $request->tanggal_mulai_filter;
        $data['tanggal_akhir'] = $request->tanggal_akhir_filter;
        $data['task_status'] = $request->task_status_filter;
        $data['pay_status'] = $request->pay_status_filter;
        $title = 'Data Task - ' . date('YmdHis') . '.xlsx';
        return Excel::download(new TaskExport($data), $title);
    }

    public function editPay(Request $request)
    {
        request()->validate([
            'pay_worker' => 'required',
        ]);

        $data = $request->all();
        $data['margin'] = $request->price_order - $request->pay_worker;

        try {
            DB::beginTransaction();
            Task::where('id', $request->id)->update($data);
            DB::commit();

            $response['status'] = '1';
            $response['msg'] = 'Berhasil submit pengajuan';
        } catch (Exception $e) {
            DB::rollBack();

            $response['status'] = '0';
            $response['msg'] = "Gagal submit pengajuan!!";
            $response['error'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function editStatus(Request $request)
    {
        request()->validate([
            'task_status' => 'required',
        ]);

        $data = $request->all();

        try {
            DB::beginTransaction();
            Task::where('id', $request->id)->update($data);
            DB::commit();

            $response['status'] = '1';
            $response['msg'] = 'Berhasil perbarui status ';
        } catch (Exception $e) {
            DB::rollBack();

            $response['status'] = '0';
            $response['msg'] = "Gagal perbarui status !!";
            $response['error'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function addFile(Request $request)
    {
        request()->validate([
            'deskripsi' => 'required',
            'file' => 'required|file|max:50000',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::user()->id;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $file_name = 'doc_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();

            try {
                $file->storeAs('task', $file_name, 'public');
                $data['file'] = 'storage/task/' . $file_name;
            } catch (\Exception $e) {
                $result['status'] = '0';
                $result['msg'] = 'Gagal mengupload file';
                return response()->json($result);
            }
        }


        try {
            DB::beginTransaction();
            TaskDetail::create($data);
            DB::commit();

            $response['status'] = '1';
            $response['msg'] = 'Berhasil menambahkan data ';
        } catch (Exception $e) {
            DB::rollBack();

            $response['status'] = '0';
            $response['msg'] = "Gagal menambahkan data !!";
            $response['error'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function editFile(Request $request)
    {
        request()->validate([
            'deskripsi' => 'required',
            'file' => 'file|max:50000',
        ]);

        $data = $request->all();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $file_name = 'doc_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();

            try {
                $file->storeAs('task', $file_name, 'public');
                $data['file'] = 'storage/task/' . $file_name;
            } catch (\Exception $e) {
                $result['status'] = '0';
                $result['msg'] = 'Gagal mengupload file';
                return response()->json($result);
            }
        }


        try {
            DB::beginTransaction();
            TaskDetail::where('id', $request->id)->update($data);
            DB::commit();

            $response['status'] = '1';
            $response['msg'] = 'Berhasil perbarui data ';
        } catch (Exception $e) {
            DB::rollBack();

            $response['status'] = '0';
            $response['msg'] = "Gagal perbarui data !!";
            $response['error'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function deleteFile(Request $request)
    {

        try {
            DB::beginTransaction();
            TaskDetail::where('id', $request->id)->delete();
            DB::commit();

            $response['status'] = '1';
            $response['msg'] = 'Berhasil menghapus data ';
        } catch (Exception $e) {
            DB::rollBack();

            $response['status'] = '0';
            $response['msg'] = "Gagal menghapus data !!";
            $response['error'] = $e->getMessage();
        }
        return response()->json($response);
    }

    public function getByDate(Request $request)
    {
        $result['status'] = 1;
        $task = Task::select('task.id', 'users.fullname', 'users.hex', 'client.customer', 'kode_task', 'task', 'order', 'deadline', 'price_order', 'pay_worker', 'margin', 'task_status', 'pay_status')
            ->leftJoin('users', 'task.worker_id', '=', 'users.id')
            ->leftJoin('client', 'task.client_id', '=', 'client.id');

        // Calendar requests normally include both dates. Keep them optional so
        // direct requests without a date range do not pass null to whereDate().
        if ($request->filled('start')) {
            $task->whereDate('deadline', '>=', $request->input('start'));
        }
        if ($request->filled('end')) {
            $task->whereDate('deadline', '<=', $request->input('end'));
        }

        if ($request->has('worker_id') && $request->worker_id != '' && $request->worker_id != 'all') {
            $task->where('worker_id', $request->worker_id);
        }
        if (Auth::user()->role == 'Worker') {
            $task->where('worker_id', Auth::user()->id);
        }

        $result['task'] = $task->get();

        return response()->json($result);
    }
}
