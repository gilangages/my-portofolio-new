<script setup>
// Tidak perlu logic khusus, hanya tampilan
</script>

<template>
  <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-white">
    <div class="flex flex-col items-center gap-6 p-8">
      <div class="relative animate-bounce-slow">
        <div
          class="bg-black text-white px-8 py-4 border-4 border-black relative z-10 shadow-[8px_8px_0px_0px_rgba(150,150,150,0.5)] transform -rotate-2">
          <h1 class="text-4xl md:text-6xl font-black italic tracking-tighter font-[Inter]">LOADING!!</h1>
        </div>
        <div
          class="absolute -bottom-4 left-1/2 -translate-x-1/2 w-0 h-0 border-l-[15px] border-l-transparent border-r-[15px] border-r-transparent border-t-[20px] border-t-black"></div>
      </div>

      <div class="w-64 h-6 border-4 border-black p-1 bg-white mt-8 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <div class="h-full bg-black animate-fill-bar w-full origin-left"></div>
      </div>

      <p class="text-sm font-bold font-mono animate-pulse mt-2 lowercase tracking-tight text-gray-600">
        initializing environment...
      </p>
    </div>
  </div>
</template>

<style scoped>
/* Custom Keyframes untuk animasi yang unik */
@keyframes fill-bar {
  0% {
    transform: scaleX(0);
  }
  50% {
    transform: scaleX(0.7);
  }
  100% {
    transform: scaleX(1);
  }
}

.animate-fill-bar {
  animation: fill-bar 1.5s ease-in-out infinite;
}

.animate-bounce-slow {
  animation: bounce 2s infinite;
}

@keyframes bounce {
  0%,
  100% {
    transform: translateY(-5%);
    animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
  }
  50% {
    transform: translateY(0);
    animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
  }
}
</style>
