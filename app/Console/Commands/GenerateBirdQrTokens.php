<?php

namespace App\Console\Commands;

use App\Models\Bird;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GenerateBirdQrTokens extends Command
{
    protected $signature = 'birds:generate-qr-tokens';
    protected $description = 'Genera tokens QR para todas las aves que no los tienen';

    public function handle()
    {
        $this->info('Generando tokens QR para aves...');
        
        // Obtener todas las aves sin qr_token
        $birdsWithoutToken = Bird::whereNull('qr_token')->orWhere('qr_token', '')->get();
        
        if ($birdsWithoutToken->isEmpty()) {
            $this->info('✅ Todas las aves ya tienen tokens QR.');
            return 0;
        }
        
        $this->info("Encontradas {$birdsWithoutToken->count()} aves sin token QR.");
        $bar = $this->output->createProgressBar($birdsWithoutToken->count());
        $bar->start();
        
        $success = 0;
        $errors = 0;
        
        foreach ($birdsWithoutToken as $bird) {
            try {
                // Generar token único
                $bird->qr_token = (string) Str::uuid();
                $bird->save();
                
                // Generar QR SVG
                try {
                    $qrUrl = route('admin.aves.show.byqr', $bird->qr_token);
                    $svg = QrCode::format('svg')->size(256)->margin(1)->generate($qrUrl);
                    $qrPath = 'qrs/ave_'.$bird->IDGallina.'_qr.svg';
                    Storage::disk('public')->put($qrPath, $svg);
                    $bird->qr_image_path = $qrPath;
                    $bird->save();
                } catch (\Throwable $qe) {
                    Log::warning('No se pudo generar QR SVG', ['id' => $bird->IDGallina, 'err' => $qe->getMessage()]);
                }
                
                $success++;
            } catch (\Throwable $e) {
                $errors++;
                Log::error('Error generando token para ave', ['id' => $bird->IDGallina, 'err' => $e->getMessage()]);
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Tokens generados exitosamente: {$success}");
        
        if ($errors > 0) {
            $this->error("❌ Errores: {$errors}");
        }
        
        return 0;
    }
}
