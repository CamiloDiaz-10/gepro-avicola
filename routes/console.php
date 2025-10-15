<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\Bird;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('birds:generate-qr {--force : Regenerar PNG incluso si ya existe} {--only-missing : Generar solo para las que no tienen imagen} ', function () {
    $force = (bool) $this->option('force');
    $onlyMissing = (bool) $this->option('only-missing');
    $count = 0; $errors = 0; $skipped = 0;
    $disk = Storage::disk('public');

    Bird::chunk(500, function ($chunk) use (&$count, &$errors, &$skipped, $disk, $force, $onlyMissing) {
        foreach ($chunk as $bird) {
            try {
                if (empty($bird->qr_token)) {
                    $bird->qr_token = (string) Str::uuid();
                }
                $qrPath = 'qrs/ave_'.$bird->IDGallina.'_qr.svg';
                if (!$force && $onlyMissing && !empty($bird->qr_image_path) && $disk->exists($bird->qr_image_path)) {
                    $skipped++;
                    continue;
                }
                $qrUrl = route('admin.aves.show.byqr', $bird->qr_token);
                $svg = QrCode::format('svg')->size(512)->margin(1)->generate($qrUrl);
                $disk->put($qrPath, $svg);
                $bird->qr_image_path = $qrPath;
                $bird->save();
                $count++;
            } catch (\Throwable $e) {
                $errors++;
                $this->error("Error con ave {$bird->IDGallina}: ".$e->getMessage());
            }
        }
    });

    $this->info("QRs generados/actualizados: $count, omitidos: $skipped, errores: $errors");
})->purpose('Genera tokens e imágenes de QR para todas las aves');

Artisan::command('birds:normalize-states', function () {
    $map = [
        'Viva' => 'Activa',
        'Fallecida' => 'Muerta',
        'Trasladada' => 'Activa',
    ];

    $total = 0;
    foreach ($map as $from => $to) {
        $affected = DB::table('gallinas')->where('Estado', $from)->update(['Estado' => $to]);
        $total += $affected;
        $this->info("Actualizadas {$affected} aves de '{$from}' a '{$to}'.");
    }
    $this->info("Total actualizadas: {$total}");
})->purpose('Normaliza estados de aves: Viva->Activa, Fallecida->Muerta, Trasladada->Activa');
