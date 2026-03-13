<script setup>
import { computed } from "vue";

const props = defineProps({
  percent: {
    type: Number,
    default: 0,
  },
});

const clampedPercent = computed(() => Math.min(100, Math.max(0, Math.round(props.percent))));

const statusText = computed(() => {
  const p = clampedPercent.value;
  if (p < 10) return "connecting to server...";
  if (p < 30) return "fetching profile data...";
  if (p < 50) return "loading projects & certificates...";
  if (p < 70) return "preparing skills & experiences...";
  if (p < 90) return "loading images...";
  if (p < 100) return "almost there...";
  return "ready!";
});
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
          class="absolute -bottom-4 left-1/2 -translate-x-1/2 w-0 h-0 border-l-[15px] border-l-transparent border-r-[15px] border-r-transparent border-t-[20px] border-t-black">
        </div>
      </div>

      <!-- Progress bar yang mengikuti persen -->
      <div class="w-64 h-6 border-4 border-black p-1 bg-white mt-8 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <div class="h-full bg-black transition-all duration-500 ease-out" :style="{ width: clampedPercent + '%' }">
        </div>
      </div>

      <!-- Percentage number -->
      <p class="text-2xl font-black font-mono tracking-tighter -mt-2">
        {{ clampedPercent }}%
      </p>

      <!-- Status text -->
      <p class="text-sm font-bold font-mono lowercase tracking-tight text-gray-600">
        {{ statusText }}
      </p>
    </div>
  </div>
</template>

<style scoped>
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