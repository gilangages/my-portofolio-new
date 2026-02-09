<script setup>
import { Icon } from "@iconify/vue";

const menus = [
  { name: "Home", href: "#", icon: "mdi:home-variant-outline" },
  { name: "About", href: "#", icon: "mdi:card-account-details-outline" },
  { name: "Projects", href: "#", icon: "mdi:folder-outline" },
  { name: "Services", href: "#", icon: "mdi:code-tags" },
  { name: "Contact", href: "#", icon: "mdi:email-outline" },
];
</script>

<template>
  <nav
    class="fixed bottom-2 md:top-6 md:bottom-auto left-1/2 -translate-x-1/2 z-50 w-fit max-w-2xl transition-all duration-300">
    <div
      class="bg-white border-2 border-black rounded-full px-6 py-2 md:px-6 md:py-2 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex justify-between items-center transition-all hover:shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5">
      <div class="hidden md:block font-serif font-bold text-xl tracking-tighter mr-4">A</div>

      <div class="flex gap-4 md:gap-2 items-center">
        <a
          v-for="item in menus"
          :key="item.name"
          :href="item.href"
          class="group flex items-center justify-center gap-1.5 p-2 md:px-3 md:py-1.5 rounded-full text-sm font-medium transition-all duration-200 border border-transparent hover:bg-black hover:text-white hover:border-black active:scale-90">
          <Icon
            :icon="item.icon"
            class="w-6 h-6 md:w-4 md:h-4 opacity-100 md:opacity-70 group-hover:opacity-100 transition-opacity" />

          <span class="hidden md:block">{{ item.name }}</span>
        </a>
      </div>
    </div>
  </nav>
</template>
