@extends('layouts.app-with-sidebar')

@section('title', 'Detalle del Ave y Código QR')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto space-y-6">
        @if(session('success'))
            <div class="p-3 rounded bg-green-100 text-green-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="p-3 rounded bg-red-100 text-red-700">{{ session('error') }}</div>
        @endif
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Detalle del Ave</h1>
            <div class="flex gap-2">
                @php
                    $isAdmin = auth()->check() && optional(auth()->user()->role)->NombreRol === 'Administrador';
                    $isOwner = auth()->check() && optional(auth()->user()->role)->NombreRol === 'Propietario';
                @endphp
                @if($isAdmin)
                    <a href="{{ route('admin.aves.index') }}" class="px-3 py-2 text-sm rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">Volver</a>
                @else
                    <button type="button" id="btnBackSafe" class="px-3 py-2 text-sm rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">Volver</button>
                @endif
                @if($isAdmin || $isOwner)
                    <a href="#editar-estado" class="px-3 py-2 text-sm rounded-md bg-emerald-600 text-white hover:bg-emerald-700">Editar estado</a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 bg-white shadow rounded-lg p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-gray-500">ID</div>
                        <div class="font-semibold">{{ $bird->IDGallina }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Estado</div>
                        <div class="font-semibold">{{ $bird->Estado }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Lote</div>
                        <div class="font-semibold">{{ $bird->lote->Nombre ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Tipo</div>
                        <div class="font-semibold">{{ $bird->tipoGallina->Nombre ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Nacimiento</div>
                        <div class="font-semibold">{{ $bird->FechaNacimiento ? \Carbon\Carbon::parse($bird->FechaNacimiento)->format('d/m/Y') : '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Peso (g)</div>
                        <div class="font-semibold">{{ $bird->Peso ?? '-' }}</div>
                    </div>
                </div>
                @auth
                    @php
                        $role = optional(auth()->user()->role)->NombreRol;
                        $canEdit = in_array($role, ['Administrador','Propietario']);
                        $updateRouteName = $role === 'Propietario' ? 'owner.aves.estado.update' : 'admin.aves.estado.update';
                    @endphp
                    @if($canEdit)
                        <div id="editar-estado" class="pt-4 border-t">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Actualizar Estado</h3>
                            <form method="POST" action="{{ route($updateRouteName, $bird->IDGallina) }}" class="flex items-end gap-3">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Estado</label>
                                    <select name="Estado" class="rounded-md border-gray-300">
                                        @foreach(['Activa','Muerta','Vendida'] as $estado)
                                            <option value="{{ $estado }}" {{ $bird->Estado === $estado ? 'selected' : '' }}>{{ $estado }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="px-3 py-2 rounded-md bg-green-600 text-white hover:bg-green-700">Guardar cambios</button>
                            </form>
                        </div>
                    @endif
                @endauth
                @if(!empty($bird->UrlImagen))
                    <div class="pt-4">
                        <div class="text-xs text-gray-500 mb-1">Foto del Ave</div>
                        <img src="{{ asset('storage/'.$bird->UrlImagen) }}" alt="Foto del ave" class="rounded border max-h-72 object-contain">
                        <div class="text-xs text-gray-500 mt-1">Archivo: {{ $bird->UrlImagen }}</div>
                    </div>
                @endif
            </div>
            <div class="bg-white shadow rounded-lg p-6 space-y-3">
                <div class="text-sm text-gray-600">Código QR</div>
                @if(!empty($bird->qr_image_path))
                    <img src="{{ asset('storage/'.$bird->qr_image_path) }}" alt="QR del ave" class="mx-auto rounded border">
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span class="break-all">Token: {{ $bird->qr_token }}</span>
                    </div>
                    <a href="{{ asset('storage/'.$bird->qr_image_path) }}" download class="block text-center px-3 py-2 text-sm rounded-md bg-green-600 text-white hover:bg-green-700">Descargar (SVG)</a>
                    <button id="btnDownloadPngStored" class="w-full px-3 py-2 text-sm rounded-md bg-green-600 text-white hover:bg-green-700">Descargar PNG</button>
                    
                    @auth
                        @if(optional(auth()->user()->role)->NombreRol === 'Administrador')
                            <form action="{{ route('admin.aves.qr.regenerate', $bird->qr_token) }}" method="POST" class="pt-2">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 text-sm rounded-md bg-gray-700 text-white hover:bg-gray-800">Regenerar QR</button>
                            </form>
                        @endif
                    @endauth
                @else
                    <div id="qr-container" class="flex items-center justify-center p-2 border rounded"></div>
                    <div class="text-xs text-gray-500 break-all">Token: {{ $bird->qr_token }}</div>
                    <a id="downloadLink" download="ave_{{ $bird->IDGallina }}_qr.png" class="block text-center px-3 py-2 text-sm rounded-md bg-green-600 text-white hover:bg-green-700">Descargar PNG</a>
                    @auth
                        @if(optional(auth()->user()->role)->NombreRol === 'Administrador')
                            <form action="{{ route('admin.aves.qr.regenerate', $bird->qr_token) }}" method="POST" class="pt-2">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 text-sm rounded-md bg-gray-700 text-white hover:bg-gray-800">Generar PNG en servidor</button>
                            </form>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnBackSafe = document.getElementById('btnBackSafe');
        if (btnBackSafe) {
            btnBackSafe.addEventListener('click', function() {
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    // Fallback público: ir al inicio
                    window.location.href = @json(route('home'));
                }
            });
        }
        const storedImg = document.querySelector('img[alt="QR del ave"]');
        const downloadPngStoredBtn = document.getElementById('btnDownloadPngStored');
        if (storedImg) {
            // Descargar como PNG (conversión desde SVG)
            if (downloadPngStoredBtn) {
                downloadPngStoredBtn.addEventListener('click', async function() {
                    try {
                        const imgUrl = storedImg.src;
                        const resp = await fetch(imgUrl, { cache: 'no-store' });
                        const svgText = await resp.text();
                        const svgBlob = new Blob([svgText], { type: 'image/svg+xml;charset=utf-8' });
                        const url = URL.createObjectURL(svgBlob);
                        const img = new Image();
                        img.onload = function() {
                            const scale = 4; // mejorar resolución
                            const canvas = document.createElement('canvas');
                            canvas.width = img.width * scale;
                            canvas.height = img.height * scale;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                            const pngUrl = canvas.toDataURL('image/png');
                            const a = document.createElement('a');
                            a.href = pngUrl;
                            a.download = 'ave_{{ $bird->IDGallina }}_qr.png';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                        };
                        img.src = url;
                    } catch (err) {
                        console.error('No se pudo convertir a PNG', err);
                    }
                });
            }
            return; // No usar QR de cliente si ya hay imagen en servidor
        }

        // Fallback: generar QR en cliente solo si existe contenedor
        const container = document.getElementById('qr-container');
        if (!container) return;
        const url = @json(route('admin.aves.show.byqr', $bird->qr_token));
        // Cargar librería solo si la necesitamos
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
        script.onload = function() {
            window.QRCode.toCanvas(url, { width: 256, margin: 1 }, function (error, canvas) {
                if (error) {
                    console.error(error);
                    container.textContent = 'Error generando el QR';
                    return;
                }
                container.innerHTML = '';
                container.appendChild(canvas);

                const link = document.getElementById('downloadLink');
                if (link) {
                    link.addEventListener('click', function() {
                        link.href = canvas.toDataURL('image/png');
                    });
                }
            });
        };
        document.head.appendChild(script);
    });
    </script>
@endpush
