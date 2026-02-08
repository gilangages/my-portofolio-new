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
</script>
<template>
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
          <Icon :icon="item.icon" class="w-4 h-4 md:w-4 md:h-4 opacity-70 group-hover:opacity-100 transition-opacity" />
          <span>{{ item.name }}</span>
        </a>
      </div>
    </div>
  </nav>
</template>
