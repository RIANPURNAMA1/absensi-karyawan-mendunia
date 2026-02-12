<div id="modalKameraAbsen" class="fixed inset-0 z-[9999] bg-black hidden flex-col">
    <video id="videoPreviewAbsen" class="h-full w-full object-cover" autoplay playsinline></video>
    
    <div class="absolute inset-0 flex flex-col justify-between p-6">
        <div class="flex justify-end">
            <button onclick="hentikanKameraAbsen()" class="bg-black/20 backdrop-blur-md p-2 rounded-full text-white shadow-lg">
                <i data-lucide="x" class="w-8 h-8"></i>
            </button>
        </div>

        <div class="flex flex-col items-center gap-4 pb-12">
            <div class="bg-black/50 text-white text-xs px-4 py-1.5 rounded-full backdrop-blur-sm mb-2">
                Posisikan wajah di tengah layar
            </div>
            <button id="btnShutterAbsen" onclick="eksekusiAmbilFoto()" 
                class="w-20 h-20 bg-white border-[6px] border-white/30 rounded-full active:scale-90 transition shadow-2xl">
                <div class="w-full h-full rounded-full border-2 border-gray-400"></div>
            </button>
        </div>
    </div>

    <canvas id="canvasSimpanFoto" class="hidden"></canvas>
</div>