<?php

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Task;
use App\Support\CountryList;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ClientController extends BaseController
{
    public function index()
    {
        $data = [];
        $this->loadThemePreferences($data);
        $data['title'] = 'Data Client';
        $data['page'] = 'client';
        $data['countries'] = CountryList::all();
        return view('area.client', $data);
    }

    public function list(Request $request)
    {
        $client = Client::query();

        if (!$request->has('order')) {
            $client->orderBy('created_at', 'desc');
        }

        $datatable = DataTables::eloquent($client)
            ->addIndexColumn();

        return $datatable->make(true);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('term');

        $data = Client::where('customer', 'like', '%' . $searchTerm . '%')->get();

        $results = [];
        foreach ($data as $item) {
            $results[] = [
                'id' => $item->id,
                'text' => $item->customer
            ];
        }

        return response()->json($results);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'customer' => 'required',
            'jk' => 'required',
            'handphone_country' => 'nullable|string|size:2',
            'handphone' => ['nullable', 'string', 'max:255', function ($attribute, $value, $fail) {
                if ($value !== null && $value !== '' && !in_array(trim((string) $value), ['0', '-'], true) && !Client::normalizeHandphone($value, $request->input('handphone_country', 'ID'))) {
                    $fail('Nomor handphone harus berupa nomor Indonesia yang valid.');
                }
            }],
            'asal' => 'nullable|string|max:255',
        ]);

        $data = [
            'handphone_country' => $validated['handphone_country'] ?? 'ID',
            'handphone' => $validated['handphone'] ?? null,
            'customer' => $validated['customer'],
            'jk' => $validated['jk'],
            'asal' => $validated['asal'] ?? null,
        ];
        $data['user_id'] = Auth::user()->id;

        try {
            DB::beginTransaction();
            Client::create($data);
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
        $validated = $request->validate([
            'id' => 'required|exists:client,id',
            'customer' => 'required',
            'jk' => 'required',
            'handphone_country' => 'nullable|string|size:2',
            'handphone' => ['nullable', 'string', 'max:255', function ($attribute, $value, $fail) {
                if ($value !== null && $value !== '' && !in_array(trim((string) $value), ['0', '-'], true) && !Client::normalizeHandphone($value, $request->input('handphone_country', 'ID'))) {
                    $fail('Nomor handphone harus berupa nomor Indonesia yang valid.');
                }
            }],
            'asal' => 'nullable|string|max:255',
        ]);

        $data = [
            'handphone_country' => $validated['handphone_country'] ?? 'ID',
            'handphone' => $validated['handphone'] ?? null,
            'customer' => $validated['customer'],
            'jk' => $validated['jk'],
            'asal' => $validated['asal'] ?? null,
        ];
        unset($data['id']);

        try {
            DB::beginTransaction();
            Client::where('id', $request->id)->update($data);
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
        $client = Client::find($id);
        if (request()->ajax()) {
            if ($client != null) {
                $response['status'] = 1;
                $response['client'] = $client;
                $response['msg'] = "Data ditemukan";
            } else {
                $response['status'] = 0;
                $response['msg'] = "Data tidak ditemukan!!";
            }
        } else {
            if ($client != null) {
                $client->total = Task::where('client_id', $id)->count();
                $client->pay = Task::where('client_id', $id)->sum('price_order');
                $client->margin = Task::where('client_id', $id)->sum('margin');

                $data = [];
                $this->loadThemePreferences($data);
                $data['title'] = 'Data Client';
                $data['page'] = 'client';
                $data['client'] = $client;
                return view('area.client_detail', $data);
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
            Client::where('id', $request->id)->delete();
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
