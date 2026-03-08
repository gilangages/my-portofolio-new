<script setup>
import { Icon } from "@iconify/vue";
import { ref, onMounted, onUnmounted, watch } from "vue";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { useRoute } from "vue-router";

gsap.registerPlugin(ScrollTrigger);

const route = useRoute();
const menus = [
  { name: "Home", href: "/", icon: "mdi:home-variant-outline" },
  { name: "About", href: "/about", icon: "mdi:card-account-details-outline" },
  { name: "Projects", href: "/projects", icon: "mdi:folder-outline" },
  { name: "Certificates", href: "/certificates", icon: "mdi:certificate-outline" },
  { name: "Services", href: "/services", icon: "mdi:code-tags" },
  { name: "Contacts", href: "/contacts", icon: "mdi:email-outline" },
];

const menuContainer = ref(null);
const navRef = ref(null);
let footerScrollTrigger = null;

// Fungsi utama animasi
const playScrollHint = () => {
  const el = menuContainer.value;
  if (el && window.innerWidth < 768) {
    setTimeout(() => {
      el.scrollLeft = 0;
      gsap.fromTo(
        el,
        { scrollLeft: 0 },
        {
          scrollLeft: 60,
          duration: 1.2,
          delay: 0.2,
          ease: "power2.inOut",
          yoyo: true,
          repeat: 1,
          overwrite: "auto",
        },
      );
    }, 600);
  }
};

// --- FUNGSI BARU UNTUK SETUP/RESET SCROLL TRIGGER ---
const setupFooterScrollTrigger = () => {
  if (footerScrollTrigger) {
    footerScrollTrigger.kill();
    footerScrollTrigger = null;
  }

  // RESET POSISI NAVBAR
  if (navRef.value) {
    gsap.set(navRef.value, { y: 0, x: "-50%" });
    navRef.value.classList.add("transition-all", "duration-300");
  }

  // Jeda memastikan konten halaman baru di RouterView sudah selesai di-render
  setTimeout(() => {
    const footerEl = document.querySelector("footer");
    if (!footerEl || !navRef.value) return;

    footerScrollTrigger = ScrollTrigger.create({
      trigger: footerEl,
      start: "top bottom",
      end: "bottom top",
      onUpdate: () => {
        const footerRect = footerEl.getBoundingClientRect();
        const navHeight = navRef.value.offsetHeight;
        const viewportHeight = window.innerHeight;
        const isMobile = window.innerWidth < 768;

        // PROTEKSI BUG LAYOUT:
        // Jika user masih berada di bagian atas halaman (scroll < 50px),
        // abaikan semua kalkulasi dorongan. Ini mencegah navbar sembunyi
        // saat render awal atau saat footer belum turun ke bawah.
        if (window.scrollY < 50) {
          gsap.set(navRef.value, { y: 0, x: "-50%" });
          navRef.value.classList.add("transition-all", "duration-300");
          return;
        }

        navRef.value.classList.remove("transition-all", "duration-300");

        if (isMobile) {
          const navNormalBottom = viewportHeight - 16;
          if (footerRect.top < navNormalBottom) {
            const pushAmount = navNormalBottom - footerRect.top;
            gsap.set(navRef.value, { y: -pushAmount, x: "-50%" });
          } else {
            gsap.set(navRef.value, { y: 0, x: "-50%" });
            navRef.value.classList.add("transition-all", "duration-300");
          }
        } else {
          const navNormalBottomDesktop = 24 + navHeight;
          if (footerRect.top < navNormalBottomDesktop) {
            const pushAmount = navNormalBottomDesktop - footerRect.top;
            gsap.set(navRef.value, { y: -pushAmount, x: "-50%" });
          } else {
            gsap.set(navRef.value, { y: 0, x: "-50%" });
            navRef.value.classList.add("transition-all", "duration-300");
          }
        }
      },
    });

    ScrollTrigger.refresh();
  }, 500);
};

onMounted(() => {
  if (route.path === "/") {
    window.addEventListener("app-loading-done", playScrollHint, { once: true });
  } else {
    playScrollHint();
  }

  setupFooterScrollTrigger();
});

// Jika path berubah (user pindah menu), jalankan ulang fungsi kalkulasi ScrollTrigger
watch(
  () => route.path,
  () => {
    // 1. KEMBALIKAN VIEW KE ATAS SAAT GANTI HALAMAN
    window.scrollTo(0, 0);

    // 2. BERSIHKAN CACHE ANIMASI GSAP YG NYANGKUT SEBELUMNYA
    if (navRef.value) {
      gsap.killTweensOf(navRef.value);
      gsap.set(navRef.value, { clearProps: "all" });
    }

    setupFooterScrollTrigger();
  },
);

onUnmounted(() => {
  window.removeEventListener("app-loading-done", playScrollHint);
  if (footerScrollTrigger) {
    footerScrollTrigger.kill();
  }
});
</script>

<template>
  <nav
    ref="navRef"
    class="fixed bottom-4 md:top-6 md:bottom-auto left-1/2 -translate-x-1/2 z-50 w-[95%] md:max-w-fit transition-all duration-300">
    <div
      class="bg-white border-2 border-black rounded-2xl md:rounded-full px-2 py-2 md:px-6 md:py-2 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center transition-all hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
      <div class="hidden md:block font-serif font-bold text-xl tracking-tighter mr-4 border-r-2 border-black pr-4">
        A
      </div>

      <div
        ref="menuContainer"
        class="flex gap-2 items-center w-full md:w-auto overflow-x-auto no-scrollbar md:overflow-visible px-1 scroll-fade min-w-0">
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
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.no-scrollbar::-webkit-scrollbar {
  display: none;
}

.router-link-exact-active svg {
  color: white !important;
  opacity: 1 !important;
}

@media (max-width: 768px) {
  .scroll-fade {
    mask-image: linear-gradient(to right, black 80%, transparent 100%);
    -webkit-mask-image: linear-gradient(to right, black 80%, transparent 100%);
  }
}
</style>
