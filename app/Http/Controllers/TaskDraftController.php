<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Area\BaseController;
use App\Models\Client;
use App\Models\Task;
use App\Models\TaskAcademic;
use App\Models\TaskCategory;
use App\Models\TaskDraft;
use App\Models\User;
use App\Support\CountryList;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\Rules\Phone;
use Yajra\DataTables\Facades\DataTables;

class TaskDraftController extends BaseController
{
    public function create()
    {
        return view('request-task', [
            'categories' => TaskCategory::orderBy('nama')->get(),
            'countries' => CountryList::all(),
        ]);
    }

    public function lookupClient(Request $request)
    {
        $rawPhone = (string) $request->input('handphone');
        $country = strtoupper((string) $request->input('handphone_country', 'ID'));
        $phone = Client::normalizeHandphone($rawPhone, $country);
        $client = $phone ? Client::where('handphone', $phone)->first() : null;

        return response()->json([
            'found' => (bool) $client,
            'client' => $client ? ['id' => $client->id, 'customer' => $client->customer] : null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'handphone' => ['required', 'string', 'max:255', (new Phone)->countryField('handphone_country')],
            'handphone_country' => 'required|string|size:2',
            'client_id' => 'nullable|integer|exists:client,id',
            'customer' => 'nullable|string|max:255',
            'jk' => 'nullable|string|max:50',
            'asal' => 'nullable|string|max:255',
            'category_id' => 'required|exists:task_category,id',
            'task' => 'required|string',
            'order' => 'required|date',
            'deadline' => 'nullable|date|after_or_equal:order',
            'prodi' => 'nullable|string|max:255',
            'judul' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'is_lanjutan_metopen' => 'nullable|boolean',
            'belum_memiliki_judul' => 'nullable|boolean',
        ]);

        $category = TaskCategory::findOrFail($validated['category_id']);
        if (in_array($category->tipe, ['metopen', 'artikel_ilmiah'], true)) {
            $academicRules = ['prodi' => 'required|string|max:255'];
            if ($category->tipe === 'artikel_ilmiah' || !$request->boolean('belum_memiliki_judul')) {
                $academicRules['judul'] = 'required|string';
            }
            validator($request->all(), $academicRules)->validate();
        }

        $phone = Client::normalizeHandphone($validated['handphone'], $validated['handphone_country']);
        try {
            DB::beginTransaction();

            $client = null;
            if (!empty($validated['client_id'])) {
                $candidate = Client::find($validated['client_id']);
                if ($candidate && $candidate->handphone === $phone) {
                    $client = $candidate;
                }
            }
            if (!$client) {
                $client = Client::where('handphone', $phone)->first();
            }
            if (!$client) {
                validator($request->all(), [
                    'customer' => 'required|string|max:255',
                    'jk' => 'required|string|max:50',
                ])->validate();

                $client = Client::create([
                    'handphone_country' => strtoupper($validated['handphone_country']),
                    'customer' => $validated['customer'],
                    'handphone' => $phone,
                    'jk' => $validated['jk'],
                    'asal' => $validated['asal'] ?? null,
                    'user_id' => null,
                ]);
            }

            $draft = TaskDraft::create([
                'client_id' => $client->id,
                'category_id' => $category->id,
                'kode_request' => 'RQ' . date('Ymd') . strtoupper(Str::random(5)),
                'task' => $validated['task'],
                'order' => $validated['order'],
                'deadline' => $validated['deadline'] ?? null,
                'prodi' => $validated['prodi'] ?? null,
                'judul' => $validated['judul'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'is_lanjutan_metopen' => $category->tipe === 'artikel_ilmiah' && $request->boolean('is_lanjutan_metopen'),
                'status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'status' => 1,
                'msg' => 'Request berhasil dikirim',
                'kode_request' => $draft->kode_request,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 0, 'msg' => 'Request gagal dikirim. Silakan coba lagi.'], 500);
        }
    }

    public function adminIndex()
    {
        $data = [];
        $this->loadThemePreferences($data);
        $data['page'] = 'task-request';
        $data['title'] = 'Request Task';
        $data['workers'] = User::where('role', 'Worker')->orderBy('fullname')->get(['id', 'fullname']);

        return view('area.task_request', $data);
    }

    public function list(Request $request)
    {
        $drafts = TaskDraft::with(['client', 'category'])
            ->where('status', $request->input('status', 'pending'))
            ->latest();

        return DataTables::eloquent($drafts)
            ->addIndexColumn()
            ->addColumn('customer', fn (TaskDraft $draft) => $draft->client->customer ?? '-')
            ->addColumn('handphone', fn (TaskDraft $draft) => $draft->client->handphone ?? '-')
            ->addColumn('category_name', fn (TaskDraft $draft) => $draft->category->nama ?? '-')
            ->editColumn('created_at', fn (TaskDraft $draft) => $draft->created_at ? $draft->created_at->format('d M Y H:i') : '-')
            ->make(true);
    }

    public function detail($id)
    {
        $draft = TaskDraft::with(['client', 'category', 'task.worker', 'task.academic'])->findOrFail($id);
        return response()->json(['status' => 1, 'draft' => $draft]);
    }

    public function assign(Request $request, $id)
    {
        $validated = $request->validate([
            'worker_id' => 'required|exists:users,id',
            'price_order' => 'nullable|numeric|min:0',
            'pay_worker' => 'nullable|numeric|min:0',
            'judul' => 'nullable|string',
        ]);

        $draft = TaskDraft::with(['category'])->where('status', 'pending')->findOrFail($id);

        if ($draft->category->tipe === 'metopen') {
            validator($request->all(), ['judul' => 'required|string'])->validate();
        }

        try {
            DB::beginTransaction();
            $count = Task::withTrashed()->whereDate('created_at', date('Y-m-d'))->count();
            $priceOrder = $validated['price_order'] ?? 0;
            $payWorker = $validated['pay_worker'] ?? 0;
            $academicTitle = $draft->judul;
            if ($draft->category->tipe === 'metopen') {
                $academicTitle = $validated['judul'];
            }
            $task = Task::create([
                'client_id' => $draft->client_id,
                'worker_id' => $validated['worker_id'],
                'category_id' => $draft->category_id,
                'kode_task' => 'TS' . date('Ymd') . sprintf('%03d', $count + 1),
                'task' => $draft->task,
                'order' => $draft->order,
                'deadline' => $draft->deadline,
                'price_order' => $priceOrder,
                'pay_worker' => $payWorker,
                'margin' => $priceOrder - $payWorker,
                'task_status' => 'Waiting',
                'pay_status' => 'Hold',
                'user_id' => Auth::id(),
            ]);

            if (in_array($draft->category->tipe, ['metopen', 'artikel_ilmiah'], true)) {
                TaskAcademic::create([
                    'task_id' => $task->id,
                    'prodi' => $draft->prodi,
                    'judul' => $academicTitle,
                    'keterangan' => $draft->keterangan,
                    'is_lanjutan_metopen' => $draft->is_lanjutan_metopen,
                ]);
            }

            $draft->update(['task_id' => $task->id, 'judul' => $academicTitle, 'status' => 'assigned', 'assigned_at' => now(), 'assigned_by' => Auth::id()]);
            DB::commit();

            return response()->json(['status' => 1, 'msg' => 'Request berhasil di-assign ke worker.']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 0, 'msg' => 'Request gagal di-assign.'], 500);
        }
    }

}
