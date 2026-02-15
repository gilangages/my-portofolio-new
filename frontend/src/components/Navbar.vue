<script setup>
import { Icon } from "@iconify/vue";
import { ref, onMounted } from "vue";
import gsap from "gsap"; // Import GSAP

const menus = [
  { name: "Home", href: "/", icon: "mdi:home-variant-outline" },
  { name: "About", href: "/about", icon: "mdi:card-account-details-outline" },
  { name: "Projects", href: "/projects", icon: "mdi:folder-outline" },
  { name: "Certificates", href: "/certificates", icon: "mdi:certificate-outline" },
  { name: "Services", href: "/services", icon: "mdi:code-tags" },
  { name: "Contact", href: "/contacts", icon: "mdi:email-outline" },
];

// 1. Buat Ref untuk container menu
const menuContainer = ref(null);

// 2. Animasi "Scroll Hint" saat Mounted
onMounted(() => {
  // Jalankan hanya di layar mobile (< 768px)
  if (window.innerWidth < 768 && menuContainer.value) {
    gsap.to(menuContainer.value, {
      scrollLeft: 50, // Geser scroll ke kanan 50px
      duration: 1, // Durasi gerakan
      delay: 1, // Tunggu 1 detik setelah load agar user "ngeh"
      ease: "power2.inOut",
      yoyo: true, // Kembali ke posisi awal (0)
      repeat: 1, // Ulangi gerakan maju-mundur sekali (total 2 gerakan)
    });
  }
});
</script>

<template>
  <nav
    class="fixed bottom-4 md:top-6 md:bottom-auto left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-fit transition-all duration-300">
    <div
      class="bg-white border-2 border-black rounded-2xl md:rounded-full px-2 py-2 md:px-6 md:py-2 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center transition-all hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
      <div class="hidden md:block font-serif font-bold text-xl tracking-tighter mr-4 border-r-2 border-black pr-4">
        A
      </div>

      <div
        ref="menuContainer"
        class="flex gap-2 items-center w-full md:w-auto overflow-x-auto no-scrollbar md:overflow-visible px-1 scroll-fade">
        <RouterLink
          v-for="item in menus"
          :key="item.name"
          :to="item.href"
          class="group flex-shrink-0 flex flex-col md:flex-row items-center justify-center gap-1 md:gap-1.5 p-2 md:px-3 md:py-1.5 rounded-xl md:rounded-full text-[10px] md:text-sm font-bold transition-all duration-200 border-2 border-transparent hover:border-black active:scale-95 whitespace-nowrap"
          exact-active-class="bg-black text-white border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] md:shadow-none">
          <Icon :icon="item.icon" class="w-5 h-5 md:w-4 md:h-4 transition-transform group-hover:scale-110" />

          <span class="uppercase tracking-tighter md:tracking-normal md:capitalize">
            {{ item.name }}
          </span>
        </RouterLink>
      </div>
    </div>
  </nav>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.router-link-exact-active svg {
  color: white !important;
  opacity: 1 !important;
}

@media (max-width: 768px) {
  .scroll-fade {
    /* Masking tetap kita pakai sebagai pelengkap visual */
    mask-image: linear-gradient(to right, black 85%, transparent 100%);
    -webkit-mask-image: linear-gradient(to right, black 85%, transparent 100%);
  }
}
</style>
