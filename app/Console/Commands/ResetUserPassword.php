<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password {password : Password baru untuk semua user}';
    protected $description = 'Reset password semua user di tabel users';

    public function handle(): int
    {
        $password = $this->argument('password');
        $count = User::count();

        if ($count === 0) {
            $this->error('Tidak ada user di tabel users.');
            return Command::FAILURE;
        }

        if (!$this->confirm("Yakin ingin mereset password {$count} user menjadi '{$password}'?")) {
            $this->info('Dibatalkan.');
            return Command::SUCCESS;
        }

        User::query()->update(['password' => Hash::make($password)]);

        $this->info("Berhasil! Password {$count} user telah di-reset.");
        $this->info("Username dan password baru:");
        User::select('username')->get()->each(fn($u) => $this->line("  - {$u->username} / {$password}"));

        return Command::SUCCESS;
    }
}
