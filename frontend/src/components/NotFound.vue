<script setup>
import { RouterLink } from "vue-router";
</script>

<template>
  <div class="min-h-screen bg-blue-100 flex items-center justify-center font-sans p-4">
    <div
      class="bg-white border-4 border-black p-8 max-w-lg w-full text-center shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden">
      <div class="absolute -top-10 -right-10 w-32 h-32 bg-yellow-300 rounded-full border-4 border-black z-0"></div>

      <div class="relative z-10">
        <h1 class="text-9xl font-black mb-0 text-red-500 drop-shadow-[4px_4px_0px_rgba(0,0,0,1)] transform -rotate-6">
          404
        </h1>

        <h2
          class="text-3xl font-black uppercase mb-6 tracking-tighter bg-black text-white inline-block px-2 transform rotate-2">
          PAGE NOT FOUND!
        </h2>

        <p class="font-bold font-mono text-gray-700 mb-8 text-lg border-y-2 border-black py-4 border-dashed">
          Halaman yang kamu cari sepertinya tidak ada.
        </p>

        <router-link
          to="/"
          class="group inline-block bg-yellow-400 border-4 border-black px-8 py-4 text-xl font-black uppercase tracking-wider hover:bg-yellow-300 hover:translate-y-[-4px] hover:shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center justify-center gap-2">
          <span>Kembali ke Beranda</span>
        </router-link>
      </div>
    </div>
  </div>
</template>
