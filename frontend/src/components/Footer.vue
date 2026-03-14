<script setup>
import { onMounted, onUnmounted, ref } from "vue";
import gsap from "gsap";

const footerRef = ref(null);
const canvasRef = ref(null);
const cursorGlowRef = ref(null);

// State untuk drawing
let ctx = null;
let isDrawing = false;
let lastX = 0;
let lastY = 0;

// State untuk UI
const hasDrawn = ref(false);
const isHovering = ref(false);
const isDrawingMode = ref(false); // State baru untuk mengunci interaksi canvas

const getCoords = (e) => {
  if (!canvasRef.value) return { x: 0, y: 0 };
  const rect = canvasRef.value.getBoundingClientRect();

  const clientX = e.touches ? e.touches[0].clientX : e.clientX;
  const clientY = e.touches ? e.touches[0].clientY : e.clientY;

  return {
    x: clientX - rect.left,
    y: clientY - rect.top,
  };
};

// --- Drawing Mode Handlers ---

const enableDrawingMode = () => {
  isDrawingMode.value = true;
  // Panggil resizeCanvas untuk memastikan ukuran canvas sesuai saat mode aktif
  setTimeout(() => resizeCanvas(), 0);
};

const disableDrawingMode = () => {
  isDrawingMode.value = false;
  stopDrawing(); // Pastikan tidak ada aksi menggambar yang tersangkut
};

// --- Drawing Handlers ---

const startDrawing = (e) => {
  if (!isDrawingMode.value) return; // Proteksi ganda
  isDrawing = true;
  hasDrawn.value = true; // Sembunyikan teks & munculkan tombol
  const { x, y } = getCoords(e);
  [lastX, lastY] = [x, y];

  gsap.to(cursorGlowRef.value, { scale: 1.5, duration: 0.2 });
};

const draw = (e) => {
  if (!isDrawing || !ctx || !isDrawingMode.value) return;
  if (e.touches) e.preventDefault();

  const { x, y } = getCoords(e);

  ctx.beginPath();
  ctx.moveTo(lastX, lastY);
  ctx.lineTo(x, y);
  ctx.stroke();
  [lastX, lastY] = [x, y];

  moveCursorGlow(e);
};

const stopDrawing = () => {
  isDrawing = false;
  gsap.to(cursorGlowRef.value, { scale: 1, duration: 0.2 });
};

// --- UI & Effects Handlers ---

const moveCursorGlow = (e) => {
  if (!isDrawingMode.value) return; // Cursor glow hanya bergerak jika mode drawing aktif
  const coords = getCoords(e);
  gsap.to(cursorGlowRef.value, {
    x: coords.x,
    y: coords.y,
    duration: 0.1,
    ease: "power2.out",
  });
};

const handleMouseEnter = () => {
  if (isDrawingMode.value) isHovering.value = true;
};
const handleMouseLeave = () => {
  isHovering.value = false;
  stopDrawing();
};

const handleSeeDrawAction = () => {
  // Reset kanvas dan kembalikan teks instruksi
  if (ctx && canvasRef.value) {
    ctx.clearRect(0, 0, canvasRef.value.width, canvasRef.value.height);
    hasDrawn.value = false;
  }
};

// --- Lifecycle Hooks ---

const resizeCanvas = () => {
  if (footerRef.value && canvasRef.value) {
    canvasRef.value.width = footerRef.value.offsetWidth;
    canvasRef.value.height = footerRef.value.offsetHeight;

    if (ctx) {
      ctx.strokeStyle = "#FFFFFF";
      ctx.lineJoin = "round";
      ctx.lineCap = "round";
      ctx.lineWidth = 4;
      ctx.shadowBlur = 10;
      ctx.shadowColor = "#FFFFFF";
    }

    // Jika layar di-resize, kanvas otomatis terhapus oleh browser, jadi kita reset state UI
    hasDrawn.value = false;
  }
};

onMounted(() => {
  ctx = canvasRef.value.getContext("2d");

  resizeCanvas();
  window.addEventListener("resize", resizeCanvas);

  gsap.set(cursorGlowRef.value, { xPercent: -50, yPercent: -50, scale: 0 });
});

onUnmounted(() => {
  window.removeEventListener("resize", resizeCanvas);
});
</script>

<template>
  <footer
    ref="footerRef"
    class="bg-black text-white relative overflow-hidden select-none z-50 min-h-[100dvh]"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
    @mousemove="moveCursorGlow">
    <transition
      enter-active-class="transition ease-out duration-500 transform"
      enter-from-class="opacity-0 scale-90"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-300 transform"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-90">
      <div v-if="!isDrawingMode" class="absolute inset-0 flex items-center justify-center z-30">
        <button
          @click="enableDrawingMode"
          class="bg-white text-black px-6 py-3 md:px-8 md:py-4 font-mono text-sm md:text-lg font-bold uppercase tracking-widest border border-transparent rounded-lg shadow-md hover:shadow-lg hover:-translate-y-1 transition-all">
          Start Drawing
        </button>
      </div>
    </transition>

    <div
      v-if="isDrawingMode"
      class="absolute inset-0 flex items-center justify-center pointer-events-none transition-opacity duration-700"
      :class="{ 'opacity-0': hasDrawn, 'opacity-100': !hasDrawn }">
      <h3 class="text-3xl md:text-5xl font-black uppercase tracking-widest font-mono text-center px-4">
        Draw things in here
        <span class="block text-sm md:text-xl font-normal normal-case mt-4 opacity-60 italic">
          (Use your cursor or finger)
        </span>
      </h3>
    </div>

    <canvas
      ref="canvasRef"
      class="absolute inset-0"
      :class="isDrawingMode ? 'z-20 cursor-crosshair touch-none pointer-events-auto' : 'z-0 pointer-events-none'"
      @mousedown="startDrawing"
      @mousemove="draw"
      @mouseup="stopDrawing"
      @touchstart.prevent="startDrawing"
      @touchmove.prevent="draw"
      @touchend="stopDrawing"></canvas>

    <div
      ref="cursorGlowRef"
      class="pointer-events-none absolute top-0 left-0 z-30 w-8 h-8 bg-white rounded-full mix-blend-screen filter blur-[8px] transition-opacity duration-300"
      :class="{ 'opacity-100': isHovering || isDrawing, 'opacity-0': !isHovering && !isDrawing }"></div>

    <transition
      enter-active-class="transition ease-out duration-300 transform"
      enter-from-class="opacity-0 -translate-y-4"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-200 transform"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-4">
      <div v-if="isDrawingMode && hasDrawn" class="absolute top-8 right-6 md:right-10 z-40 flex items-center gap-3">
        <button
          @click="disableDrawingMode"
          class="bg-red-500 text-white px-4 py-2 font-mono text-xs md:text-sm font-bold uppercase tracking-wider border border-transparent rounded shadow-sm hover:bg-red-600 hover:shadow-md hover:-translate-y-0.5 transition-all">
          Stop Draw
        </button>
        <button
          @click="handleSeeDrawAction"
          class="bg-white text-black px-4 py-2 font-mono text-xs md:text-sm font-bold uppercase tracking-wider border border-transparent rounded shadow-sm hover:bg-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all">
          Reset Draw
        </button>
      </div>
    </transition>
  </footer>
</template>

<style scoped>
footer {
  font-family: "JetBrains Mono", "Fira Code", monospace;
}
.touch-none {
  touch-action: none;
}
</style>
