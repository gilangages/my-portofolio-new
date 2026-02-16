<script setup>
import { onMounted, ref, nextTick, watch } from "vue";
import LoadingScreen from "../LoadingScreen.vue";
import { alertError } from "../lib/alert";
import { getAllServices } from "../lib/api/ServiceApi";
import { Icon } from "@iconify/vue";
import gsap from "gsap";

const services = ref([]);
const loading = ref(true);

// State untuk Modal Detail
const isModalOpen = ref(false);
const selectedService = ref(null);

// --- FUNCTION FETCH DATA ---
async function fetchServices() {
  loading.value = true;
  try {
    const response = await getAllServices();
    const responseBody = await response.json();

    if (response.status === 200) {
      services.value = responseBody.data || responseBody;
    } else {
      await alertError(responseBody.message);
    }
  } catch (e) {
    console.error(`Error fetch services:`, e);
  } finally {
    // Delay buatan seperti About.vue agar transisi smooth
    setTimeout(() => {
      loading.value = false;
    }, 800);
  }
}

// --- ANIMATION TRIGGER ---
watch(loading, (newVal) => {
  if (!newVal) {
    nextTick(() => {
      const tl = gsap.timeline();

      // 1. Animasi Header
      // Gunakan fromTo + autoAlpha agar jaminan muncul 100%
      tl.fromTo(
        ".page-title",
        { y: 30, autoAlpha: 0 }, // Start state (Invisible & agak di bawah)
        {
          y: 0,
          autoAlpha: 1, // End state (Visible & posisi normal)
          duration: 0.8,
          ease: "power2.out",
        },
      );

      // 2. Animasi Kartu Service
      if (services.value.length > 0) {
        tl.fromTo(
          ".service-card",
          { y: 30, autoAlpha: 0 }, // Start
          {
            y: 0,
            autoAlpha: 1, // End
            duration: 0.8,
            stagger: 0.1,
            ease: "power2.out",
          },
          "-=0.6", // Overlap dengan animasi judul
        );
      }
    });
  }
});

// --- MODAL LOGIC ---
function openModal(service) {
  selectedService.value = service;
  isModalOpen.value = true;
  document.body.style.overflow = "hidden";
}

function closeModal() {
  isModalOpen.value = false;
  document.body.style.overflow = "auto";
  setTimeout(() => {
    selectedService.value = null;
  }, 200);
}

onMounted(async () => {
  await fetchServices();
});
</script>

<template>
  <div class="min-h-screen bg-white">
    <Transition name="fade">
      <LoadingScreen v-if="loading" />
    </Transition>

    <div v-if="!loading" class="px-4 py-18 md:px-10 max-w-7xl mx-auto">
      <div class="text-center mb-16 mt-8 page-title" style="opacity: 0; visibility: hidden">
        <h1
          class="text-4xl md:text-6xl font-black font-serif uppercase tracking-wider inline-block relative border-b-8 border-black pb-2">
          <span class="relative z-10">My Services</span>
          <span class="absolute top-0 left-0 w-full h-full bg-gray-200 -z-0 -rotate-1 skew-x-12 opacity-70"></span>
        </h1>
        <p class="mt-6 font-[Inter] text-gray-600 text-sm md:text-base max-w-2xl mx-auto italic">
          "Professional solutions tailored to your needs. Ready to collaborate?"
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
        <div
          v-for="service in services"
          :key="service.id"
          class="service-card group flex flex-col bg-white border-4 border-black rounded-xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[4px] hover:translate-y-[4px] transition-all duration-200"
          style="opacity: 0; visibility: hidden">
          <div class="flex items-start justify-between mb-6">
            <div
              class="w-16 h-16 bg-black text-white flex items-center justify-center rounded-lg border-2 border-black shadow-[4px_4px_0px_0px_#9ca3af] group-hover:scale-110 transition-transform duration-300">
              <Icon :icon="service.icon || 'mdi:briefcase-outline'" class="text-3xl" />
            </div>
            <div class="text-right">
              <span
                class="inline-block bg-[#bef264] border-2 border-black px-3 py-1 text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_black] transform rotate-2">
                {{ service.price }}
              </span>
            </div>
          </div>

          <div class="flex flex-col flex-grow">
            <h3
              class="text-2xl font-black font-serif leading-tight mb-3 group-hover:underline decoration-4 underline-offset-4 decoration-black uppercase">
              {{ service.title }}
            </h3>

            <p
              class="text-sm text-gray-600 line-clamp-3 mb-6 font-medium border-l-2 border-gray-300 pl-3 leading-relaxed">
              {{ service.description }}
            </p>

            <div class="mt-auto grid grid-cols-2 gap-3 pt-4 border-t-2 border-dashed border-gray-300">
              <button
                @click="openModal(service)"
                class="col-span-1 py-2 px-2 text-xs font-bold uppercase border-2 border-black rounded bg-white hover:bg-gray-100 transition-colors flex items-center justify-center gap-1">
                <Icon icon="mdi:eye-outline" class="text-base" />
                Details
              </button>

              <a
                v-if="service.cta_link"
                :href="service.cta_link"
                target="_blank"
                class="col-span-1 py-2 px-2 text-xs font-bold uppercase border-2 border-black rounded bg-black text-white hover:bg-gray-800 transition-colors flex items-center justify-center gap-1">
                Order Now
                <Icon icon="mdi:arrow-right" class="text-base" />
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Teleport to="body">
      <div v-if="isModalOpen" class="fixed inset-0 z-[999] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeModal"></div>

        <div
          class="relative bg-white w-full max-w-xl max-h-[90vh] flex flex-col rounded-xl border-4 border-black shadow-[12px_12px_0px_0px_rgba(255,255,255,1)] animate-in fade-in zoom-in duration-200">
          <div class="flex justify-between items-start p-6 border-b-4 border-black bg-gray-100 rounded-t-lg shrink-0">
            <div class="flex items-center gap-4">
              <div
                class="w-12 h-12 bg-black text-white flex items-center justify-center rounded border-2 border-black shrink-0">
                <Icon :icon="selectedService?.icon || 'mdi:briefcase-outline'" class="text-2xl" />
              </div>
              <div>
                <h3 class="text-xl md:text-2xl font-black font-serif uppercase leading-none mb-1">
                  {{ selectedService?.title }}
                </h3>
                <span class="text-sm font-bold text-gray-500">
                  Starting at:
                  <span class="text-black bg-[#bef264] px-1 border border-black">{{ selectedService?.price }}</span>
                </span>
              </div>
            </div>
            <button
              @click="closeModal"
              class="p-2 bg-red-500 border-2 border-black text-white hover:bg-red-600 transition-colors rounded hover:shadow-[2px_2px_0px_0px_black]">
              <Icon icon="mdi:close" class="text-xl" />
            </button>
          </div>

          <div class="p-6 overflow-y-auto custom-scrollbar bg-white">
            <h4 class="font-bold uppercase text-sm mb-2 text-gray-400">Description</h4>
            <div
              class="prose prose-sm max-w-none text-gray-800 font-medium leading-relaxed whitespace-pre-line border-l-4 border-black pl-5">
              {{ selectedService?.description }}
            </div>
          </div>

          <div class="p-6 border-t-4 border-black bg-gray-100 rounded-b-lg shrink-0 flex gap-3">
            <a
              v-if="selectedService?.cta_link"
              :href="selectedService?.cta_link"
              target="_blank"
              class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold border-2 border-black rounded bg-black text-white hover:bg-white hover:text-black transition-all shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px]">
              <Icon icon="mdi:message-text-outline" class="text-xl" />
              I'm Interested
            </a>

            <button
              @click="closeModal"
              class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold text-black bg-white border-2 border-black rounded hover:bg-gray-100 transition-all shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px]">
              Close
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
/* Transisi Fade */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.6s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 12px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-left: 2px solid black;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: black;
  border: 2px solid white;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #333;
}
</style>
