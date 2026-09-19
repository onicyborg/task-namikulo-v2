<?php

namespace App\Http\Controllers\Area;

use App\Models\Client;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends BaseController
{
    public function index()
    {
        $data = [];
        $this->loadThemePreferences($data);
        $data['title'] = 'Dashboard';
        $data['page'] = 'dashboard';

        $bulan_awal = Carbon::now()->subMonths(6)->startOfMonth();
        $bulan_akhir = Carbon::now()->endOfMonth();
        $grafik = [];

        for ($date = $bulan_awal; $date->lte($bulan_akhir); $date->addMonth()) {
            $bulan = substr(bulanIndo($date->format('m')), 0, 3);
            $tahun = $date->format('Y');


            $waiting = Task::where('task_status', "Waiting")->whereMonth('order', $date->format('m'));
            $progress = Task::where('task_status', "Progress")->whereMonth('order', $date->format('m'));
            $done = Task::where('task_status', "Done")->whereMonth('order', $date->format('m'));

            if (Auth::user()->role == 'Worker') {
                $waiting = $waiting->where('worker_id', Auth::user()->id);
                $progress = $progress->where('worker_id', Auth::user()->id);
                $done = $done->where('worker_id', Auth::user()->id);
            }

            $grafik_data = [
                'bulan' => $bulan . ' ' . $tahun,
                'progress' => $progress->count(),
                'waiting' => $waiting->count(),
                'done' => $done->count(),
            ];

            $grafik[] = $grafik_data;
        }
        $data['grafik'] = $grafik;

        $waiting = Task::where('task_status', "Waiting");
        $progress = Task::where('task_status', "Progress");
        $done = Task::where('task_status', "Done");
        $total = Task::selectRaw('SUM(price_order) as total_price, SUM(pay_worker) as total_pay_worker, SUM(margin) as total_margin');

        if (Auth::user()->role == 'Worker') {
            $waiting = $waiting->where('worker_id', Auth::user()->id);
            $progress = $progress->where('worker_id', Auth::user()->id);
            $done = $done->where('worker_id', Auth::user()->id);
            $total = $total->where('worker_id', Auth::user()->id);
        }
        $total = $total->first();

        $data['statistik'] = [
            'waiting' => $waiting->count(),
            'progress' => $progress->count(),
            'done' => $done->count(),
            'price' => $total->total_price,
            'pay_worker' => $total->total_pay_worker,
            'margin' => $total->total_margin,
            'worker' => User::where('role', 'Worker')->count(),
            'client' => Client::count(),
        ];

        $timeline = Task::select('task.id', 'users.fullname', 'client.customer', 'task', 'order', 'deadline', 'task_status', 'pay_status')
            ->leftJoin('users', 'task.worker_id', '=', 'users.id')
            ->leftJoin('client', 'task.client_id', '=', 'client.id')
            ->whereDate('deadline', '>=', date('Y-m-d'))
            ->orderBy('deadline', 'ASC');
        if (Auth::user()->role == 'Worker') {
            $timeline = $timeline->where('worker_id', Auth::user()->id);
        }
        $data['timeline'] = $timeline->get();
        return view('area.dashboard', $data);
    }
}
