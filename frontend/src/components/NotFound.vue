<script setup>
import { RouterLink } from "vue-router";
import { Icon } from "@iconify/vue";
</script>

<template>
  <div class="min-h-screen bg-[#f5f5f5] flex items-center justify-center font-sans p-6 relative overflow-hidden">
    <div
      class="absolute top-[-10%] left-[-10%] w-64 h-64 bg-gray-200 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
    <div
      class="absolute bottom-[-10%] right-[-10%] w-64 h-64 bg-gray-300 rounded-full blur-3xl opacity-60 pointer-events-none"></div>

    <div
      class="bg-white border-4 border-black p-8 md:p-12 max-w-lg w-full text-center shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] relative z-10 transition-transform hover:-translate-y-1 duration-300">
      <div class="absolute top-3 left-3 w-2 h-2 bg-black rounded-full"></div>
      <div class="absolute top-3 right-3 w-2 h-2 bg-black rounded-full"></div>
      <div class="absolute bottom-3 left-3 w-2 h-2 bg-black rounded-full"></div>
      <div class="absolute bottom-3 right-3 w-2 h-2 bg-black rounded-full"></div>

      <div class="relative z-10 flex flex-col items-center">
        <Icon icon="mdi:emoticon-dead-outline" class="text-6xl md:text-7xl mb-4 text-black animate-pulse" />

        <h1 class="text-8xl md:text-9xl font-black mb-2 tracking-tighter font-[Inter]">404</h1>

        <div
          class="bg-black text-white px-6 py-2 mb-8 transform -rotate-1 inline-block shadow-[4px_4px_0px_0px_#9ca3af]">
          <h2 class="text-lg md:text-xl font-bold uppercase tracking-widest font-mono">Out of Frame</h2>
        </div>

        <p
          class="font-[Playfair_Display] italic text-gray-800 mb-10 text-lg md:text-xl leading-relaxed max-w-sm mx-auto">
          "It seems you've wandered into an empty panel. The story you are looking for hasn't been drawn yet."
        </p>

        <router-link
          to="/"
          class="group flex items-center justify-center gap-3 bg-white text-black px-8 py-3 rounded-xl border-2 border-black font-bold text-sm md:text-base uppercase tracking-wider shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:bg-black hover:text-white hover:translate-x-[2px] hover:translate-y-[2px] transition-all cursor-pointer">
          <Icon icon="mdi:arrow-left" class="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
          <span>Return Home</span>
        </router-link>
      </div>
    </div>
  </div>
</template>
