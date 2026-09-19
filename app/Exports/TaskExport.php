<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TaskExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $task = Task::select('task.id', 'users.fullname', 'client.customer', 'kode_task', 'task', 'order', 'deadline', 'price_order', 'pay_worker', 'margin', 'task_status', 'pay_status')
            ->leftJoin('users', 'task.worker_id', '=', 'users.id')
            ->leftJoin('client', 'task.client_id', '=', 'client.id')
            ->orderBy('order', 'desc')
            ->orderBy('task.created_at', 'desc');

        if ($this->data['tanggal_mulai'] != '') {
            $task->whereDate('order', '>=', $this->data['tanggal_mulai']);
        }
        if ($this->data['tanggal_akhir'] != '') {
            $task->whereDate('order', '<=', $this->data['tanggal_akhir']);
        }
        if ($this->data['task_status'] != '') {
            $task->where('task_status', $this->data['task_status']);
        }
        if ($this->data['pay_status'] != '') {
            $task->where('pay_status', $this->data['pay_status']);
        }
        if ($this->data['search'] != '') {
            $keyword = $this->data['search'];
            $task->whereRaw('LOWER(fullname) like ?', ['%' . strtolower($keyword) . '%'])
                ->orWhereRaw('LOWER(customer) like ?', ['%' . strtolower($keyword) . '%'])
                ->orWhereRaw('LOWER(kode_task) like ?', ['%' . strtolower($keyword) . '%'])
                ->orWhereRaw('LOWER(task) like ?', ['%' . strtolower($keyword) . '%'])
                ->orWhereRaw('LOWER(price_order) like ?', ['%' . strtolower($keyword) . '%'])
                ->orWhereRaw('LOWER(pay_worker) like ?', ['%' . strtolower($keyword) . '%'])
                ->orWhereRaw('LOWER(pay_worker) like ?', ['%' . strtolower($keyword) . '%'])
                ->orWhereRaw('LOWER(margin) like ?', ['%' . strtolower($keyword) . '%']);
        }

        if (Auth::user()->role == 'Worker') {
            $task = $task->where('worker_id', Auth::user()->id);
        }

        $task = $task->get();

        return $task->map(function ($row, $index) {
            if (Auth::user()->role == 'Admin') {
                return [
                    'nomor_urut' => $index + 1,
                    'kode_task' => $row->kode_task,
                    'customer' => $row->customer,
                    'fullname' => $row->fullname,
                    'task' => $row->task,
                    'order' => hariTglIndo($row->order),
                    'deadline' => hariTglIndo($row->deadline),
                    'price_order' => $row->price_order == 0 ? '0' : $row->price_order,
                    'pay_worker' => $row->pay_worker == 0 ? '0' : $row->pay_worker,
                    'margin' => $row->margin == 0 ? '0' : $row->margin,
                    'task_status' => $row->task_status,
                    'pay_status' => $row->pay_status
                ];
            } else {
                return [
                    'nomor_urut' => $index + 1,
                    'kode_task' => $row->kode_task,
                    'customer' => $row->customer,
                    'task' => $row->task,
                    'order' => hariTglIndo($row->order),
                    'deadline' => hariTglIndo($row->deadline),
                    'pay_worker' => $row->pay_worker == 0 ? '0' : $row->pay_worker,
                    'task_status' => $row->task_status,
                    'pay_status' => $row->pay_status
                ];
            }
        });
    }

    public function headings(): array
    {

        if (Auth::user()->role == 'Admin') {
            return [
                '#',
                'Kode Task',
                'Nama Customer',
                'Worker',
                'Deskripsi',
                'Tgl Order',
                'Tgl Deadline',
                'Harga',
                'Pay to Worker',
                'Margin',
                'Task Status',
                'Pay Status',
            ];
        } else {
            return [
                '#',
                'Kode Task',
                'Nama Customer',
                'Deskripsi',
                'Tgl Order',
                'Tgl Deadline',
                'Pay to Worker',
                'Task Status',
                'Pay Status',
            ];
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:L1')->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '70309f'],
                    ],
                ]);

                $event->sheet->getStyle('A1:L' . $event->sheet->getHighestRow())
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);
            },
        ];
    }
}
