<script setup>
import { onMounted, ref, nextTick } from "vue";
import Navbar from "../Navbar.vue";
import LoadingScreen from "../LoadingScreen.vue"; // Import LoadingScreen
import { alertError } from "../lib/alert";
import { getAllCertificates } from "../lib/api/CertificateApi";
import { Icon } from "@iconify/vue";
import gsap from "gsap";

const certificates = ref([]);
const loading = ref(true);

// State untuk Modal Detail
const isModalOpen = ref(false);
const selectedCert = ref(null);

// --- FUNCTION FETCH DATA ---
async function fetchCertificates() {
  loading.value = true;
  try {
    const response = await getAllCertificates();
    const responseBody = await response.json();

    if (response.status === 200) {
      certificates.value = responseBody.data || responseBody;

      // Jalankan animasi setelah DOM di-update dengan data baru
      await nextTick();
      animateCertificates();
    } else {
      await alertError(responseBody.message);
    }
  } catch (e) {
    console.error(`Error fetch certificates:`, e);
  } finally {
    loading.value = false;
  }
}

// --- GSAP ANIMATION ---
function animateCertificates() {
  const tl = gsap.timeline();

  // Animasi Header
  tl.from(".page-title", {
    y: -50,
    opacity: 0,
    duration: 0.8,
    ease: "back.out(1.7)",
  });

  // Animasi Kartu (Stagger)
  tl.from(
    ".cert-card",
    {
      y: 100,
      opacity: 0,
      duration: 0.8,
      stagger: 0.1, // Jeda antar kartu
      ease: "power2.out",
    },
    "-=0.4",
  );
}

// --- MODAL LOGIC ---
function openModal(cert) {
  selectedCert.value = cert;
  isModalOpen.value = true;
  document.body.style.overflow = "hidden"; // Disable scroll body
}

function closeModal() {
  isModalOpen.value = false;
  document.body.style.overflow = "auto"; // Enable scroll body
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
    <Navbar />

    <div class="px-4 py-18 md:px-10 max-w-7xl mx-auto">
      <div class="text-center mb-16 mt-8 page-title">
        <h1
          class="text-4xl md:text-6xl font-black font-serif uppercase tracking-wider inline-block relative border-b-8 border-black pb-2">
          <span class="relative z-10">All Certificates</span>
          <span class="absolute top-0 left-0 w-full h-full bg-gray-200 -z-0 -rotate-1 skew-x-12 opacity-70"></span>
        </h1>
        <p class="mt-6 font-[Inter] text-gray-600 text-sm md:text-base max-w-2xl mx-auto italic">
          "A complete archive of validated skills, workshops, and achievements."
        </p>
      </div>

      <LoadingScreen v-if="loading" />

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
        <div
          v-for="certificate in certificates"
          :key="certificate.id"
          class="cert-card group flex flex-col bg-white border-4 border-black rounded-xl p-4 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[4px] hover:translate-y-[4px] transition-all duration-200">
          <div
            class="w-full aspect-video bg-gray-50 border-2 border-black rounded-lg mb-5 overflow-hidden relative flex items-center justify-center p-4">
            <img
              :src="certificate.image_url"
              :alt="certificate.title"
              class="relative z-10 w-full h-full object-contain transition-transform duration-300 group-hover:scale-105" />
          </div>

          <div class="flex flex-col flex-grow">
            <div class="mb-2">
              <span
                class="inline-block bg-black text-white text-xs font-bold px-2 py-1 rounded border border-black uppercase tracking-wider">
                {{ certificate.issuer }}
              </span>
            </div>

            <h3
              class="text-xl font-bold font-serif leading-tight mb-3 group-hover:underline decoration-2 underline-offset-2">
              {{ certificate.title }}
            </h3>

            <p class="text-sm text-gray-600 line-clamp-3 mb-4 font-medium flex-grow border-l-2 border-gray-300 pl-3">
              {{ certificate.description }}
            </p>

            <div class="flex gap-2 mt-auto pt-4 border-t-2 border-dashed border-gray-300">
              <button
                @click="openModal(certificate)"
                class="flex-1 py-2 px-3 text-xs font-bold uppercase border-2 border-black rounded bg-white hover:bg-gray-100 transition-colors flex items-center justify-center gap-1">
                <Icon icon="mdi:eye-outline" class="text-base" />
                Detail
              </button>

              <a
                v-if="certificate.credential_link"
                :href="certificate.credential_link"
                target="_blank"
                class="flex-1 py-2 px-3 text-xs font-bold uppercase border-2 border-black rounded bg-black text-white hover:bg-gray-800 transition-colors flex items-center justify-center gap-1">
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
          class="relative bg-white w-full max-w-2xl max-h-[90vh] flex flex-col rounded-xl border-4 border-black shadow-[10px_10px_0px_0px_rgba(255,255,255,1)] animate-in fade-in zoom-in duration-200">
          <div class="flex justify-between items-start p-6 border-b-4 border-black bg-gray-100 rounded-t-lg shrink-0">
            <div>
              <h3 class="text-2xl md:text-3xl font-black font-serif uppercase leading-none mb-2">
                {{ selectedCert?.title }}
              </h3>
              <span class="text-sm font-bold bg-white px-3 py-1 border-2 border-black shadow-[2px_2px_0px_0px_black]">
                Issued by: {{ selectedCert?.issuer }}
              </span>
            </div>
            <button
              @click="closeModal"
              class="p-2 bg-red-500 border-2 border-black text-white hover:bg-red-600 transition-colors rounded hover:shadow-[2px_2px_0px_0px_black]">
              <Icon icon="mdi:close" class="text-xl" />
            </button>
          </div>

          <div class="p-6 overflow-y-auto custom-scrollbar bg-white">
            <div
              class="w-full aspect-video bg-gray-50 border-2 border-black rounded-lg mb-6 overflow-hidden flex items-center justify-center p-4">
              <img :src="selectedCert?.image_url" :alt="selectedCert?.title" class="w-full h-full object-contain" />
            </div>

            <div
              class="prose prose-sm md:prose-base max-w-none text-gray-800 font-medium leading-relaxed whitespace-pre-line border-l-4 border-black pl-4">
              {{ selectedCert?.description }}
            </div>
          </div>

          <div class="p-6 border-t-4 border-black bg-gray-100 rounded-b-lg shrink-0 flex flex-col md:flex-row gap-3">
            <a
              v-if="selectedCert?.credential_link"
              :href="selectedCert?.credential_link"
              target="_blank"
              class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold border-2 border-black rounded bg-white hover:bg-black hover:text-white transition-all shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px]">
              <Icon icon="mdi:certificate-outline" class="text-xl" />
              Verify Credential
            </a>
            <button
              @click="closeModal"
              class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold text-white bg-red-600 border-2 border-black rounded hover:bg-red-700 transition-all shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px]">
              Close Details
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
/* Custom Scrollbar untuk Modal */
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
