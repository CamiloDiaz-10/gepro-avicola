@extends('layouts.app-with-sidebar')

@section('title', 'Escanear Código QR - Aves')

@section('content')
<div class="p-6" x-data>
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800">Escanear Código QR</h1>
                <a href="{{ route('admin.aves.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Volver</a>
            </div>
            <p class="text-sm text-gray-600">Apunta la cámara al QR del ave para ver su información.</p>

            <div class="space-y-3">
                <div id="perm-hint" class="hidden p-3 rounded bg-yellow-50 text-yellow-700 text-sm">
                    Da permiso de cámara en el navegador. Si negaste antes, ve a la barra de URL → icono de cámara → Permitir.
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <label class="text-sm text-gray-700">Cámara:</label>
                    <select id="cameraSelect" class="rounded-md border-gray-300 min-w-[220px]"></select>
                    <button id="btnPermission" class="px-3 py-2 rounded-md bg-amber-600 text-white hover:bg-amber-700">Permitir cámara</button>
                    <button id="btnStart" class="px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Iniciar</button>
                    <button id="btnStop" class="px-3 py-2 rounded-md bg-gray-600 text-white hover:bg-gray-700" disabled>Detener</button>
                </div>
                <div id="qr-reader" class="w-full"></div>
                <div id="qr-result" class="text-sm text-gray-700"></div>
            </div>

            <div class="pt-4 border-t">
                <div class="text-sm font-medium text-gray-700 mb-2">¿Problemas con la cámara? Ingresa el token manualmente</div>
                <div class="flex gap-2">
                    <input id="manualToken" type="text" placeholder="Pega aquí el token o la URL completa del QR"
                           class="flex-1 rounded-md border-gray-300" />
                    <button id="goManual" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Ir</button>
                </div>
                <div class="text-xs text-gray-500 mt-2">El token es un UUID como 123e4567-e89b-12d3-a456-426614174000 o una URL que contenga /aves/qr/{token}.</div>
            </div>

            <div class="text-xs text-gray-500">Si no funciona la cámara, verifica permisos del navegador y usa HTTPS o <code>http://127.0.0.1</code>.</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', async function () {
        const qrRegionId = "qr-reader";
        const resultDiv = document.getElementById('qr-result');
        const manualInput = document.getElementById('manualToken');
        const goManualBtn = document.getElementById('goManual');
        const permHint = document.getElementById('perm-hint');
        const select = document.getElementById('cameraSelect');
        const btnStart = document.getElementById('btnStart');
        const btnStop = document.getElementById('btnStop');
        const btnPermission = document.getElementById('btnPermission');
        let html5Qr;
        let currentId;

        async function ensureLibLoaded() {
            if (typeof Html5Qrcode !== 'undefined') return true;
            const sources = [
                // Local fallback (public/js/html5-qrcode.min.js)
                '/js/html5-qrcode.min.js',
                // CDNs
                'https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js',
                'https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/minified/html5-qrcode.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js',
            ];
            for (const src of sources) {
                const ok = await new Promise((resolve) => {
                    const s = document.createElement('script');
                    s.src = src;
                    s.async = true;
                    s.onload = () => resolve(true);
                    s.onerror = () => resolve(false);
                    document.head.appendChild(s);
                });
                if (ok && typeof Html5Qrcode !== 'undefined') return true;
            }
            return false;
        }

        function extractToken(text) {
            // 1) Try URL pattern .../aves/qr/{uuid}
            let m = text.match(/\/aves\/qr\/([0-9a-fA-F-]{36})/);
            if (m && m[1]) return m[1];
            // 2) If full URL with query/fragment, strip and re-check
            try {
                const u = new URL(text);
                const m2 = u.pathname.match(/\/aves\/qr\/([0-9a-fA-F-]{36})/);
                if (m2 && m2[1]) return m2[1];
            } catch(_) {}
            // 3) If it's a raw UUID
            if (/^[0-9a-fA-F-]{36}$/.test(text)) return text;
            return null;
        }

        function navigateWithToken(text) {
            const token = extractToken(text);
            if (!token) {
                resultDiv.textContent = 'No se pudo extraer un token válido del QR.';
                return;
            }
            resultDiv.textContent = 'QR leído: ' + token;
            const base = "{{ route('admin.aves.show.byqr', ':token') }}".replace(':token', token);
            const url = base + '?from=admin';
            window.location.href = url;
        }

        function onScanSuccess(decodedText, decodedResult) {
            navigateWithToken(decodedText);
        }
        function onScanError(errorMessage) { /* ruido normal de detección */ }

        async function loadCameras() {
            const ok = await ensureLibLoaded();
            if (!ok) {
                resultDiv.textContent = 'No se pudo cargar la librería de escaneo. Revisa tu conexión a Internet o el bloqueador de contenido.';
                return;
            }
            try {
                const devices = await Html5Qrcode.getCameras();
                select.innerHTML = '';
                if (!devices || devices.length === 0) {
                    select.innerHTML = '<option>No se encontraron cámaras</option>';
                    permHint.classList.remove('hidden');
                    return;
                }
                devices.forEach((d, idx) => {
                    const opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = d.label || `Cámara ${idx+1}`;
                    select.appendChild(opt);
                });
                // Heurística: elegir cámara trasera si está disponible
                const back = Array.from(select.options).find(o => /back|trasera|rear|environment/i.test(o.textContent));
                if (back) select.value = back.value;
            } catch (err) {
                // Si aquí cae, es típicamente por permisos
                permHint.classList.remove('hidden');
            }
        }

        async function requestPermission() {
            try {
                permHint.classList.add('hidden');
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                // Detener inmediatamente (solo para conceder permiso)
                stream.getTracks().forEach(t => t.stop());
                await loadCameras();
            } catch (err) {
                permHint.classList.remove('hidden');
                resultDiv.textContent = 'Permiso denegado o no disponible: ' + (err && err.message ? err.message : err);
            }
        }

        async function startScanner() {
            try {
                permHint.classList.add('hidden');
                const ok = await ensureLibLoaded();
                if (!ok) {
                    resultDiv.textContent = 'No se pudo cargar la librería de escaneo. Revisa tu conexión o intenta nuevamente.';
                    return;
                }
                if (!html5Qr) html5Qr = new Html5Qrcode(qrRegionId);
                currentId = select.value;
                await html5Qr.start(
                    { deviceId: { exact: currentId } },
                    { fps: 10, qrbox: 250 },
                    onScanSuccess,
                    onScanError
                );
                btnStart.disabled = true;
                btnStop.disabled = false;
            } catch (err) {
                resultDiv.textContent = 'No se pudo iniciar la cámara: ' + (err && err.message ? err.message : err);
                permHint.classList.remove('hidden');
            }
        }

        async function stopScanner() {
            try {
                if (html5Qr && html5Qr.isScanning) {
                    await html5Qr.stop();
                    await html5Qr.clear();
                }
            } finally {
                btnStart.disabled = false;
                btnStop.disabled = true;
            }
        }

        btnStart.addEventListener('click', startScanner);
        btnStop.addEventListener('click', stopScanner);
        btnPermission.addEventListener('click', requestPermission);
        await loadCameras();

        goManualBtn.addEventListener('click', function() {
            if (!manualInput.value) return;
            navigateWithToken(manualInput.value.trim());
        });
        manualInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (!manualInput.value) return;
                navigateWithToken(manualInput.value.trim());
            }
        });
    });
</script>
@endpush
