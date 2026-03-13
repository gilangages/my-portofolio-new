<script setup>
import { onMounted, ref, nextTick, watch } from "vue";
import LoadingScreen from "../LoadingScreen.vue";
import { alertError } from "../../lib/alert";
import { getAllCertificates } from "../../lib/api/CertificateApi";
import { Icon } from "@iconify/vue";
import gsap from "gsap";
import { marked } from "marked";

const certificates = ref([]);
const loading = ref(true);

// State untuk Modal Detail
const isModalOpen = ref(false);
const selectedCert = ref(null);
const renderMarkdown = (text) => {
  if (!text) return "";
  return marked.parse(text, { breaks: true });
};

// --- FUNCTION FETCH DATA (Dengan Delay Buatan) ---
async function fetchCertificates() {
  loading.value = true;
  try {
    const response = await getAllCertificates();
    const responseBody = await response.json();

    if (response.status === 200) {
      certificates.value = responseBody.data || responseBody;
    } else {
      await alertError(responseBody.message);
    }
  } catch (e) {
    console.error(`Error fetch certificates:`, e);
  } finally {
    // Delay buatan 800ms agar transisi smooth
    setTimeout(() => {
      loading.value = false;
    }, 800);
  }
}

// --- ANIMATION TRIGGER (Menggunakan Watcher) ---
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

      // 2. Animasi Kartu (Jika ada data)
      if (certificates.value.length > 0) {
        tl.fromTo(
          ".cert-card",
          { y: 30, autoAlpha: 0 },
          {
            y: 0,
            autoAlpha: 1,
            duration: 0.8,
            stagger: 0.1,
            ease: "power2.out",
          },
          "-=0.6", // Overlap dengan header
        );
      }
    });
  }
});

// --- MODAL LOGIC (Tidak Berubah) ---
function openModal(cert) {
  selectedCert.value = cert;
  isModalOpen.value = true;
  document.body.style.overflow = "hidden";
}

function closeModal() {
  isModalOpen.value = false;
  document.body.style.overflow = "auto";
  setTimeout(() => {
    selectedCert.value = null;
  }, 200);
}

onMounted(async () => {
  await fetchCertificates();
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
          <span class="relative z-10">All Certificates</span>
          <span class="absolute top-0 left-0 w-full h-full bg-gray-200 -z-0 -rotate-1 skew-x-12 opacity-70"></span>
        </h1>
        <p class="mt-4 font-[Inter] text-gray-600 text-sm md:text-base max-w-xl mx-auto italic">
          "A complete archive of validated skills, workshops, and achievements."
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-20">
        <div v-for="certificate in certificates" :key="certificate.id"
          class="cert-card group flex flex-col bg-white border-2 border-black rounded-lg p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[2px] hover:translate-y-[2px] transition-all duration-200"
          style="opacity: 0; visibility: hidden">
          <div
            class="w-full aspect-video bg-gray-50 border-2 border-black rounded-md mb-4 overflow-hidden relative flex items-center justify-center p-3">
            <img :src="certificate.image_url" :alt="certificate.title"
              class="relative z-10 w-full h-full object-contain transition-transform duration-300 group-hover:scale-105" />
          </div>

          <div class="flex flex-col flex-grow">
            <div class="mb-2">
              <span
                class="inline-block bg-black text-white text-[10px] font-bold px-1.5 py-0.5 rounded border border-black uppercase tracking-wider">
                {{ certificate.issuer }}
              </span>
            </div>

            <h3
              class="text-lg md:text-xl font-bold font-serif leading-tight mb-2 group-hover:underline decoration-2 underline-offset-2">
              {{ certificate.title }}
            </h3>

            <div v-html="renderMarkdown(certificate.description)"
              class="markdown-preview text-xs md:text-sm text-gray-600 line-clamp-3 mb-4 font-medium flex-grow border-l-2 border-gray-300 pl-2">
            </div>

            <div class="flex gap-2 mt-auto pt-3 border-t-2 border-dashed border-gray-300">
              <button @click="openModal(certificate)"
                class="flex-1 py-1.5 px-3 text-xs md:text-sm font-bold uppercase border-2 border-black rounded bg-white hover:bg-gray-100 transition-colors flex items-center justify-center gap-1">
                <Icon icon="mdi:eye-outline" class="text-base" />
                Detail
              </button>

              <a v-if="certificate.credential_link" :href="certificate.credential_link" target="_blank"
                class="flex-1 py-1.5 px-3 text-xs md:text-sm font-bold uppercase border-2 border-black rounded bg-black text-white hover:bg-gray-800 transition-colors flex items-center justify-center gap-1">
                Verify
                <Icon icon="mdi:external-link" class="text-base" />
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
          class="relative bg-white w-full max-w-2xl max-h-[90vh] flex flex-col rounded-lg border-2 border-black shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] animate-in fade-in zoom-in duration-200">
          <div
            class="flex justify-between items-start p-4 md:p-5 border-b-2 border-black bg-gray-100 rounded-t-lg shrink-0">
            <div>
              <h3 class="text-xl md:text-3xl font-black font-serif uppercase leading-none mb-2">
                {{ selectedCert?.title }}
              </h3>
              <span
                class="text-xs md:text-sm font-bold bg-white px-2 py-1 border-2 border-black shadow-[2px_2px_0px_0px_black]">
                Issued by: {{ selectedCert?.issuer }}
              </span>
            </div>
            <button @click="closeModal"
              class="p-1.5 bg-red-500 border-2 border-black text-white hover:bg-red-600 transition-colors rounded hover:shadow-[2px_2px_0px_0px_black]">
              <Icon icon="mdi:close" class="text-lg" />
            </button>
          </div>

          <div class="p-4 md:p-5 overflow-y-auto custom-scrollbar bg-white">
            <div
              class="w-full aspect-video bg-gray-50 border-2 border-black rounded-md mb-4 md:mb-5 overflow-hidden flex items-center justify-center p-3 shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)]">
              <img :src="selectedCert?.image_url" :alt="selectedCert?.title" class="w-full h-full object-contain" />
            </div>

            <div v-html="renderMarkdown(selectedCert?.description)"
              class="markdown-preview font-mono text-sm md:text-base text-gray-700 leading-relaxed"></div>
          </div>

          <div
            class="p-4 md:p-5 border-t-2 border-black bg-gray-100 rounded-b-lg shrink-0 flex flex-col sm:flex-row gap-2.5">
            <a v-if="selectedCert?.credential_link" :href="selectedCert?.credential_link" target="_blank"
              class="flex-1 flex items-center justify-center gap-2 py-2 text-xs md:text-sm font-bold border-2 border-black rounded bg-white hover:bg-black hover:text-white transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px]">
              <Icon icon="mdi:certificate-outline" class="text-lg" />
              Verify Credential
            </a>
            <button @click="closeModal"
              class="flex-1 flex items-center justify-center gap-2 py-2 text-xs md:text-sm font-bold text-white bg-red-600 border-2 border-black rounded hover:bg-red-700 transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px]">
              Close Details
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
/* Transisi Fade untuk Loading */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.6s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Custom Scrollbar untuk Modal - Dirampingkan */
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