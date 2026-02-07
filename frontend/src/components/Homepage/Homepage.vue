<script setup>
import { Icon } from "@iconify/vue";

// Menambahkan icon sesuai request
const menus = [
  { name: "Home", href: "#", icon: "mdi:home-variant-outline" }, // Icon Rumah
  { name: "About", href: "#", icon: "mdi:card-account-details-outline" }, // Icon Kartu/Data Diri
  { name: "Projects", href: "#", icon: "mdi:folder-outline" }, // Icon Folder
  { name: "Services", href: "#", icon: "mdi:code-tags" }, // Icon <> (Code)
  { name: "Contact", href: "#", icon: "mdi:email-outline" }, // Icon Email
];

const socials = [
  { name: "Github", icon: "mdi:github", href: "#" },
  { name: "Instagram", icon: "mdi:instagram", href: "#" },
  { name: "Discord", icon: "ic:baseline-discord", href: "#" },
  { name: "LinkedIn", icon: "mdi:linkedin", href: "#" },
  { name: "Youtube", icon: "mdi:youtube", href: "#" },
];
</script>

<template>
  <div
    class="min-h-screen bg-white text-black font-sans selection:bg-black selection:text-white overflow-x-hidden flex flex-col">
    <nav class="fixed top-4 md:top-6 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-2xl">
      <div
        class="bg-white border-2 border-black rounded-full px-4 py-1.5 md:px-6 md:py-2 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex justify-between items-center transition-all hover:shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5">
        <div class="hidden md:block font-serif font-bold text-xl tracking-tighter mr-4">GA.</div>

        <div class="flex gap-1 md:gap-2 overflow-x-auto no-scrollbar w-full md:w-auto justify-between md:justify-end">
          <a
            v-for="item in menus"
            :key="item.name"
            :href="item.href"
            class="group flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs md:text-sm font-medium transition-all duration-200 border border-transparent hover:bg-black hover:text-white hover:border-black active:scale-95 whitespace-nowrap">
            <Icon
              :icon="item.icon"
              class="w-4 h-4 md:w-4 md:h-4 opacity-70 group-hover:opacity-100 transition-opacity" />
            <span>{{ item.name }}</span>
          </a>
        </div>
      </div>
    </nav>

    <main class="container mx-auto px-6 pt-24 pb-6 md:pt-8 md:pb-0 min-h-screen flex items-center justify-center">
      <div class="flex flex-col-reverse md:flex-row items-center justify-between w-full max-w-4xl gap-8 md:gap-2 mt-8">
        <div class="flex-1 flex flex-col items-start space-y-3 md:space-y-3 mt-4">
          <div
            class="inline-block bg-gray-200 border-2 border-black px-3 py-1 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transform -rotate-1">
            <h5 class="font-bold text-[10px] md:text-xs tracking-wide uppercase">Available for Work</h5>
          </div>

          <div class="space-y-0.5">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight font-[Inter] tracking-tight">
              Hi, I'm
              <br class="hidden md:block" />
              <span class="underline decoration-4 underline-offset-4 decoration-black">Gilang Abdian Anggara</span>
            </h1>
            <h2 class="text-xl md:text-2xl lg:text-3xl font-[Playfair_Display] italic text-gray-800 pt-1">
              Frontend Developer
            </h2>
          </div>

          <p class="text-sm text-justify leading-relaxed max-w-lg font-[Inter] text-gray-700">
            Possimus ut numquam praesentium iste laudantium dolores deserunt inventore, enim dignissimos at porro!
            Distinctio repudiandae ad deserunt optio error vero.
          </p>

          <div class="flex flex-wrap gap-2 font-medium text-xs">
            <div class="flex items-center gap-1.5 py-1">
              <Icon icon="mdi:map-marker" />
              <span>Based in Indonesia</span>
            </div>
            <div class="flex items-center gap-1.5 px-2.5 py-1 text-black">
              <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
              <span>Available Now</span>
            </div>
          </div>

          <div class="flex gap-3 pt-1 w-full md:w-auto">
            <button
              class="flex-1 md:flex-none flex items-center justify-center gap-2 bg-black text-white px-5 py-2 rounded-xl border-2 border-black font-bold text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-white hover:text-black hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[3px] active:translate-y-[3px] transition-all">
              <Icon icon="mdi:handshake-outline" class="w-4 h-4 md:w-5 md:h-5" />
              <span>Hire Me</span>
            </button>

            <button
              class="flex-1 md:flex-none flex items-center justify-center gap-2 bg-white text-black px-5 py-2 rounded-xl border-2 border-black font-bold text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-gray-100 hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[3px] active:translate-y-[3px] transition-all">
              <Icon icon="mdi:file-download-outline" class="w-4 h-4 md:w-5 md:h-5" />
              <span>Download CV</span>
            </button>
          </div>

          <hr class="w-full border-t-2 border-gray-400 my-2" />

          <div class="flex flex-col md:flex-row items-start md:items-center gap-2 text-xs font-bold">
            <span class="whitespace-nowrap">Follow me:</span>
            <div class="flex flex-wrap gap-2">
              <a
                v-for="social in socials"
                :key="social.name"
                :href="social.href"
                class="p-1.5 border-2 border-black rounded-lg hover:bg-black hover:text-white transition-colors duration-200"
                :title="social.name">
                <Icon :icon="social.icon" class="w-4 h-4" />
              </a>
            </div>
          </div>
        </div>

        <div class="w-full md:w-5/12 flex justify-center md:justify-end relative">
          <div class="absolute inset-0 bg-gray-100 rounded-full scale-90 blur-3xl -z-10 opacity-50"></div>

          <img
            src="/me.png"
            alt="Gilang Abdian"
            class="w-[400px] md:w-[300px] -mt-12 md:mt-0 h-auto object-cover grayscale contrast-110 border-b-4 border-black" />
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
.font-serif {
  font-family: "DM Serif Display", serif;
}
</style>
