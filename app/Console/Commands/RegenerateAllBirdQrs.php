<?php

namespace App\Console\Commands;

use App\Models\Bird;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RegenerateAllBirdQrs extends Command
{
    protected $signature = 'birds:regenerate-all-qrs {--force : Forzar regeneración sin confirmación}';
    protected $description = 'Regenera TODOS los códigos QR de las aves (útil cuando hay QRs antiguos)';

    public function handle()
    {
        $this->warn('⚠️  ADVERTENCIA: Este comando regenerará TODOS los códigos QR de las aves.');
        $this->warn('    Los QR antiguos dejarán de funcionar.');
        
        if (!$this->option('force')) {
            if (!$this->confirm('¿Deseas continuar?', false)) {
                $this->info('Operación cancelada.');
                return 0;
            }
        }
        
        $this->info('Regenerando códigos QR para todas las aves...');
        
        $birds = Bird::all();
        
        if ($birds->isEmpty()) {
            $this->warn('No hay aves en el sistema.');
            return 0;
        }
        
        $this->info("Procesando {$birds->count()} aves...");
        $bar = $this->output->createProgressBar($birds->count());
        $bar->start();
        
        $success = 0;
        $errors = 0;
        
        foreach ($birds as $bird) {
            try {
                // Generar nuevo token único
                $bird->qr_token = (string) Str::uuid();
                
                // Generar QR SVG
                try {
                    $qrUrl = route('admin.aves.show.byqr', $bird->qr_token);
                    $svg = QrCode::format('svg')->size(256)->margin(1)->generate($qrUrl);
                    $qrPath = 'qrs/ave_'.$bird->IDGallina.'_qr.svg';
                    
                    // Eliminar QR antiguo si existe
                    if ($bird->qr_image_path && Storage::disk('public')->exists($bird->qr_image_path)) {
                        Storage::disk('public')->delete($bird->qr_image_path);
                    }
                    
                    Storage::disk('public')->put($qrPath, $svg);
                    $bird->qr_image_path = $qrPath;
                } catch (\Throwable $qe) {
                    Log::warning('No se pudo generar QR SVG', ['id' => $bird->IDGallina, 'err' => $qe->getMessage()]);
                }
                
                $bird->save();
                $success++;
            } catch (\Throwable $e) {
                $errors++;
                Log::error('Error regenerando QR para ave', ['id' => $bird->IDGallina, 'err' => $e->getMessage()]);
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        $this->info("✅ QRs regenerados exitosamente: {$success}");
        
        if ($errors > 0) {
            $this->error("❌ Errores: {$errors}");
        }
        
        $this->newLine();
        $this->info('🎉 Proceso completado. Ahora puedes imprimir los nuevos códigos QR desde el sistema.');
        
        return 0;
    }
}
