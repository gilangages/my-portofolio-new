<script setup>
import { onMounted, ref, nextTick, watch } from "vue";
import LoadingScreen from "../LoadingScreen.vue";
import { alertError } from "../../lib/alert";
import { getAllServices } from "../../lib/api/ServiceApi";
import { Icon } from "@iconify/vue";
import gsap from "gsap";
import { marked } from "marked";

const services = ref([]);
const loading = ref(true);

// State untuk Modal Detail
const isModalOpen = ref(false);
const selectedService = ref(null);
const renderMarkdown = (text) => {
  if (!text) return "";
  return marked.parse(text, { breaks: true });
};

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
      window.dispatchEvent(new CustomEvent("content-loaded"));
    }, 800);
  }
}

// --- ANIMATION TRIGGER ---
watch(loading, (newVal) => {
  if (!newVal) {
    nextTick(() => {
      const tl = gsap.timeline();

      // 1. Animasi Header
      tl.fromTo(
        ".page-title",
        { y: 30, autoAlpha: 0 },
        {
          y: 0,
          autoAlpha: 1,
          duration: 0.8,
          ease: "power2.out",
        },
      );

      // 2. Animasi Kartu Service atau Coming Soon
      if (services.value.length > 0) {
        tl.fromTo(
          ".service-card",
          { y: 30, autoAlpha: 0 },
          {
            y: 0,
            autoAlpha: 1,
            duration: 0.8,
            stagger: 0.1,
            ease: "power2.out",
          },
          "-=0.6",
        );
      } else {
        // Animasi untuk box Coming Soon
        tl.fromTo(
          ".coming-soon-box",
          { y: 30, autoAlpha: 0 },
          {
            y: 0,
            autoAlpha: 1,
            duration: 0.8,
            ease: "power2.out",
          },
          "-=0.6",
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

// --- ORDER / CTA LOGIC ---
function getOrderLink(service) {
  if (service?.cta_link && service.cta_link.startsWith("http")) {
    return service.cta_link;
  }
  const phoneNumber = "6285798124873";
  const message = `Hello Abdian, I am interested in your service: *${service?.title}*.\n\nCould we discuss the details and requirements for this project?`;

  return `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
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

    <div v-if="!loading" class="-mt-16 md:mt-4 px-4 py-16 md:px-8 max-w-6xl mx-auto">
      <div class="text-center mb-12 mt-4 page-title" style="opacity: 0; visibility: hidden">
        <h1
          class="text-3xl md:text-5xl font-black font-serif uppercase tracking-wider inline-block relative border-b-4 border-black pb-2">
          <span class="relative z-10">All Services</span>
          <span class="absolute top-0 left-0 w-full h-full bg-gray-200 -z-0 -rotate-1 skew-x-12 opacity-70"></span>
        </h1>
        <p class="mt-4 font-[Inter] text-gray-600 text-sm md:text-base max-w-xl mx-auto italic">
          "Professional solutions tailored to your needs. Ready to collaborate?"
        </p>
      </div>

      <div v-if="services.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-20">
        <div
          v-for="service in services"
          :key="service.id"
          class="service-card group flex flex-col bg-white border-2 border-black rounded-lg p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[2px] hover:translate-y-[2px] transition-all duration-200"
          style="opacity: 0; visibility: hidden">
          <div class="flex items-start justify-between mb-4">
            <div
              class="w-12 h-12 bg-black text-white flex items-center justify-center rounded-md border-2 border-black shadow-[2px_2px_0px_0px_#9ca3af] group-hover:scale-105 transition-transform duration-300">
              <Icon :icon="service.icon || 'mdi:briefcase-outline'" class="text-2xl" />
            </div>
            <div class="text-right">
              <span
                class="inline-block bg-[#bef264] border-2 border-black px-2 py-0.5 text-[10px] md:text-xs font-black uppercase rounded shadow-[1px_1px_0px_0px_black] transform rotate-2">
                {{ service.price }}
              </span>
            </div>
          </div>

          <div class="flex flex-col flex-grow">
            <h3
              class="text-lg md:text-xl font-black font-serif leading-tight mb-2 group-hover:underline decoration-2 underline-offset-4 decoration-black uppercase">
              {{ service.title }}
            </h3>

            <div
              v-html="renderMarkdown(service.description)"
              class="markdown-preview text-xs md:text-sm text-gray-600 line-clamp-3 mb-4 font-medium border-l-2 border-gray-300 pl-2 leading-relaxed"></div>

            <div class="mt-auto grid grid-cols-2 gap-2 pt-3 border-t-2 border-dashed border-gray-300">
              <button
                @click="openModal(service)"
                class="col-span-1 py-1.5 px-2 text-[10px] md:text-xs font-bold uppercase border-2 border-black rounded bg-white hover:bg-gray-100 transition-colors flex items-center justify-center gap-1">
                <Icon icon="mdi:eye-outline" class="text-sm" />
                Details
              </button>

              <a
                :href="getOrderLink(service)"
                target="_blank"
                class="col-span-1 py-1.5 px-2 text-[10px] md:text-xs font-bold uppercase border-2 border-black rounded bg-black text-white hover:bg-gray-800 transition-colors flex items-center justify-center gap-1">
                Order Now
                <Icon icon="mdi:arrow-right" class="text-sm" />
              </a>
            </div>
          </div>
        </div>
      </div>

      <div
        v-else
        class="coming-soon-box flex flex-col items-center justify-center py-16 px-6 text-center border-2 border-black bg-white rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] max-w-2xl mx-auto mb-20"
        style="opacity: 0; visibility: hidden">
        <div
          class="w-16 h-16 bg-black text-white flex items-center justify-center rounded-lg border-2 border-black shadow-[2px_2px_0px_0px_#9ca3af] mb-4">
          <Icon icon="mdi:cone" class="text-3xl" />
        </div>
        <h2 class="text-2xl md:text-3xl font-black font-serif uppercase tracking-wider mb-3">Coming Soon</h2>
        <p class="font-[Inter] text-gray-600 text-sm md:text-base font-medium max-w-md leading-relaxed mb-6">
          I am currently cooking up some exciting new service packages. They are being carefully crafted and will be
          available here shortly!
        </p>
        <router-link
          to="/contacts"
          class="inline-flex items-center gap-2 py-2 px-4 text-xs md:text-sm font-bold uppercase border-2 border-black rounded bg-black text-white hover:bg-white hover:text-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px]">
          <Icon icon="mdi:email-outline" class="text-base" />
          Discuss Custom Project
        </router-link>
      </div>
    </div>

    <Teleport to="body">
      <div v-if="isModalOpen" class="fixed inset-0 z-[999] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeModal"></div>

        <div
          class="relative bg-white w-full max-w-lg max-h-[90vh] flex flex-col rounded-lg border-2 border-black shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] animate-in fade-in zoom-in duration-200">
          <div
            class="flex justify-between items-start p-4 md:p-5 border-b-2 border-black bg-gray-100 rounded-t-md shrink-0">
            <div class="flex items-center gap-3">
              <div
                class="w-10 h-10 bg-black text-white flex items-center justify-center rounded border-2 border-black shrink-0">
                <Icon :icon="selectedService?.icon || 'mdi:briefcase-outline'" class="text-xl" />
              </div>
              <div>
                <h3 class="text-lg md:text-xl font-black font-serif uppercase leading-none mb-1">
                  {{ selectedService?.title }}
                </h3>
                <span class="text-xs font-bold text-gray-500">
                  Starting at:
                  <span class="text-black bg-[#bef264] px-1 border border-black">{{ selectedService?.price }}</span>
                </span>
              </div>
            </div>
            <button
              @click="closeModal"
              class="p-1.5 bg-red-500 border-2 border-black text-white hover:bg-red-600 transition-colors rounded hover:shadow-[2px_2px_0px_0px_black]">
              <Icon icon="mdi:close" class="text-lg" />
            </button>
          </div>

          <div class="p-4 md:p-5 overflow-y-auto custom-scrollbar bg-white">
            <h4 class="font-bold uppercase text-xs mb-2 text-gray-400">Description</h4>
            <div
              v-html="renderMarkdown(selectedService?.description)"
              class="markdown-preview font-mono text-sm md:text-base text-gray-700 leading-relaxed"></div>
          </div>

          <div class="p-4 md:p-5 border-t-2 border-black bg-gray-100 rounded-b-md shrink-0 flex gap-2.5">
            <a
              :href="getOrderLink(selectedService)"
              target="_blank"
              class="flex-1 flex items-center justify-center gap-2 py-2 text-xs md:text-sm font-bold border-2 border-black rounded bg-black text-white hover:bg-white hover:text-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px]">
              <Icon icon="mdi:message-text-outline" class="text-lg" />
              I'm Interested
            </a>

            <button
              @click="closeModal"
              class="flex-1 flex items-center justify-center gap-2 py-2 text-xs md:text-sm font-bold text-black bg-white border-2 border-black rounded hover:bg-gray-100 transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px]">
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

/* Scrollbar Dirampingkan */
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-left: 2px solid black;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: black;
  border: 1px solid white;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #333;
}

/* Styling khusus untuk isi konten Markdown di Modal */
.markdown-preview :deep(ul) {
  list-style-type: disc !important;
  margin-left: 1.5rem !important;
  margin-bottom: 0.5rem !important;
}
.markdown-preview :deep(ol) {
  list-style-type: decimal !important;
  margin-left: 1.5rem !important;
  margin-bottom: 0.5rem !important;
}
.markdown-preview :deep(li) {
  display: list-item !important;
  margin-bottom: 0.25rem;
}
.markdown-preview :deep(p) {
  margin-bottom: 0.75rem;
}
.markdown-preview :deep(strong),
.markdown-preview :deep(b) {
  font-weight: 900 !important;
}
.markdown-preview :deep(em),
.markdown-preview :deep(i) {
  font-style: italic !important;
}
</style>
