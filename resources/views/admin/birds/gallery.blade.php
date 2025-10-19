@extends('layouts.app-with-sidebar')

@push('styles')
<style>
    /* Lightbox Styles */
    .lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        z-index: 1000;
        text-align: center;
        padding: 20px;
        box-sizing: border-box;
    }
    
    .lightbox.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .lightbox-content {
        max-width: 90%;
        max-height: 90vh;
        position: relative;
    }
    
    .lightbox-img {
        max-height: 80vh;
        max-width: 100%;
        object-fit: contain;
    }
    
    .lightbox-close {
        position: absolute;
        top: -40px;
        right: 0;
        color: white;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
        background: rgba(0, 0, 0, 0.7);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
    
    .lightbox-info {
        color: white;
        margin-top: 15px;
        font-size: 16px;
    }
    
    .lightbox-nav {
        position: absolute;
        top: 50%;
        width: 100%;
        display: flex;
        justify-content: space-between;
        padding: 0 20px;
        box-sizing: border-box;
        transform: translateY(-50%);
        pointer-events: none;
    }
    
    .lightbox-nav button {
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: auto;
    }
    
    .lightbox-nav button:focus {
        outline: none;
    }
    .modal-content {
        max-width: 90%;
        max-height: 90vh;
        width: auto;
        height: auto;
        position: relative;
    }
    .modal-image {
        max-width: 100%;
        max-height: 80vh;
        width: auto;
        height: auto;
        display: block;
        margin: 0 auto;
    }
    .close-button {
        position: absolute;
        top: -40px;
        right: 0;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        background: rgba(0, 0, 0, 0.5);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bird-info {
        color: white;
        text-align: center;
        margin-top: 10px;
    }
</style>
@endpush

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Galería de Aves</h2>
        </div>

        @if($birds->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
                <i class="fas fa-dove text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-300">No hay imágenes de aves disponibles.</p>
            </div>
        @else
            <!-- Lightbox Container -->
            <div id="lightbox" class="lightbox">
                <div class="lightbox-content">
                    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
                    <img id="lightbox-img" class="lightbox-img" src="" alt="Imagen de ave">
                    <div id="lightbox-info" class="lightbox-info"></div>
                    <div class="lightbox-nav">
                        <button onclick="changeImage(-1)">❮</button>
                        <button onclick="changeImage(1)">❯</button>
                    </div>
                </div>
            </div>

            <!-- Gallery Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($birds as $index => $bird)
                    @if($bird->UrlImagen && file_exists(public_path('storage/' . $bird->UrlImagen)))
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden hover:shadow-lg transition-transform duration-300 hover:scale-105">
                            <div class="relative cursor-pointer" 
                                 onclick="openLightbox('{{ asset('storage/' . $bird->UrlImagen) }}', '{{ $bird->IDGallina }}', '{{ $bird->IDLote }}', {{ $index }})">
                                <img src="{{ asset('storage/' . $bird->UrlImagen) }}" 
                                     alt="Ave #{{ $bird->IDGallina }}" 
                                     class="w-full h-48 object-cover">
                            </div>
                            <div class="p-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    ID: {{ $bird->IDGallina }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Lote: {{ $bird->IDLote }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                            <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex flex-col items-center justify-center">
                                <i class="fas fa-dove text-4xl text-gray-400"></i>
                                <p class="text-xs text-gray-500 mt-2">Sin imagen</p>
                            </div>
                            <div class="p-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    ID: {{ $bird->IDGallina }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Lote: {{ $bird->IDLote }}
                                </p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-6">
                {{ $birds->links() }}
            </div>
        @endif
    </div>
</div>
@push('scripts')
<script>
    // Variables globales para el lightbox
    let currentImageIndex = 0;
    let images = [];
    
    // Inicializar el array de imágenes
    document.addEventListener('DOMContentLoaded', function() {
        const imageElements = document.querySelectorAll('.grid img[src]');
        images = Array.from(imageElements).map(img => ({
            src: img.src,
            id: img.alt.replace('Ave #', ''),
            lote: img.closest('.relative').nextElementSibling?.querySelector('.text-xs')?.textContent.replace('Lote: ', '') || ''
        }));
        
        // Agregar evento de teclado para navegación
        document.addEventListener('keydown', handleKeyDown);
    });
    
    // Función para abrir el lightbox
    function openLightbox(src, id, lote, index) {
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const lightboxInfo = document.getElementById('lightbox-info');
        
        currentImageIndex = index;
        lightboxImg.src = src;
        lightboxImg.alt = `Ave #${id}`;
        lightboxInfo.innerHTML = `ID: ${id} | Lote: ${lote}`;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden'; // Evitar scroll en el fondo
    }
    
    // Función para cerrar el lightbox
    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        lightbox.classList.remove('active');
        document.body.style.overflow = ''; // Restaurar scroll
    }
    
    // Función para cambiar de imagen
    function changeImage(direction) {
        currentImageIndex += direction;
        
        // Circular navigation
        if (currentImageIndex >= images.length) {
            currentImageIndex = 0;
        } else if (currentImageIndex < 0) {
            currentImageIndex = images.length - 1;
        }
        
        const image = images[currentImageIndex];
        const lightboxImg = document.getElementById('lightbox-img');
        const lightboxInfo = document.getElementById('lightbox-info');
        
        lightboxImg.src = image.src;
        lightboxImg.alt = `Ave #${image.id}`;
        lightboxInfo.innerHTML = `ID: ${image.id} | Lote: ${image.lote}`;
    }
    
    // Manejar eventos de teclado
    function handleKeyDown(e) {
        const lightbox = document.getElementById('lightbox');
        
        if (!lightbox.classList.contains('active')) return;
        
        switch(e.key) {
            case 'Escape':
                closeLightbox();
                break;
            case 'ArrowLeft':
                changeImage(-1);
                break;
            case 'ArrowRight':
                changeImage(1);
                break;
        }
    }
    
    // Cerrar al hacer clic fuera de la imagen
    document.getElementById('lightbox')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });
</script>
@endpush

@endsection
