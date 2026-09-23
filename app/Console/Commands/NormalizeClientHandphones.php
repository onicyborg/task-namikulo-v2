<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;

class NormalizeClientHandphones extends Command
{
    protected $signature = 'client:normalize-handphones {--dry-run : Tampilkan perubahan tanpa menyimpan data}';
    protected $description = 'Membersihkan nomor handphone client menjadi format +62xxxxxxxxxxx';

    public function handle(): int
    {
        $updated = 0;
        $skipped = 0;
        $dryRun = (bool) $this->option('dry-run');

        Client::withTrashed()
            ->whereNotNull('handphone')
            ->where('handphone', '!=', '')
            ->chunkById(100, function ($clients) use (&$updated, &$skipped, $dryRun) {
                foreach ($clients as $client) {
                    $rawHandphone = trim((string) $client->getRawOriginal('handphone'));

                    if (in_array($rawHandphone, ['0', '-'], true)) {
                        $this->line("#{$client->id}: {$rawHandphone} -> NULL");
                        $updated++;

                        if (!$dryRun) {
                            $client->handphone = null;
                            $client->save();
                        }

                        continue;
                    }

                    $country = $client->getRawOriginal('handphone_country') ?: 'ID';
                    $normalized = Client::normalizeHandphone($rawHandphone, $country);
                    $detectedCountry = Client::countryForHandphone($rawHandphone, $country);

                    if (!$normalized || !$detectedCountry) {
                        $skipped++;
                        continue;
                    }

                    if ($normalized === $client->getRawOriginal('handphone')
                        && $detectedCountry === $client->getRawOriginal('handphone_country')) {
                        continue;
                    }

                    $countryChange = $client->getRawOriginal('handphone_country') !== $detectedCountry
                        ? " [{$client->getRawOriginal('handphone_country')} -> {$detectedCountry}]"
                        : '';
                    $this->line("#{$client->id}: {$client->getRawOriginal('handphone')} -> {$normalized}{$countryChange}");
                    $updated++;

                    if (!$dryRun) {
                        $client->handphone_country = $detectedCountry;
                        $client->handphone = $normalized;
                        $client->save();
                    }
                }
            });

        $this->info(($dryRun ? '[Dry run] ' : '') . "{$updated} nomor akan/dan telah dinormalisasi.");
        if ($skipped > 0) {
            $this->warn("{$skipped} data dilewati karena tidak memiliki format nomor handphone yang valid.");
        }

        return self::SUCCESS;
    }
}
