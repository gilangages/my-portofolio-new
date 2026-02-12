<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";
import { getAllCertificates } from "../../lib/api/CertificateApi";
import { Icon } from "@iconify/vue";

const certificates = ref([]);
const loading = ref(true);

// State untuk Modal
const isModalOpen = ref(false);
const selectedCert = ref(null);

async function fetchCertificates() {
  loading.value = true;
  try {
    const response = await getAllCertificates({ featured: 1 });
    const responseBody = await response.json();
    console.log(responseBody);

    if (response.status === 200) {
      certificates.value = responseBody.data;
    } else {
      console.error(responseBody.message);
    }
  } catch (error) {
    console.error("Failed to fetch certificates");
  } finally {
    loading.value = false;
  }
}

// --- NEW LOGIC: FEATURED CERTIFICATES (LIMIT 3) ---
// Best Practice: Gunakan computed untuk memanipulasi tampilan data tanpa mengubah data aslinya
const featuredCertificates = computed(() => {
  return certificates.value.slice(0, 3);
});

// --- LOGIC MODAL & BACK BUTTON (Sama seperti FeaturedProject) ---

function openModal(cert) {
  selectedCert.value = cert;
  isModalOpen.value = true;
  document.body.style.overflow = "hidden";

  // HISTORY API
  window.history.pushState({ modalOpen: true }, "", "");
  window.addEventListener("popstate", handlePopstate);
}

function handlePopstate() {
  isModalOpen.value = false;
  document.body.style.overflow = "auto";
  setTimeout(() => {
    selectedCert.value = null;
  }, 200);
  window.removeEventListener("popstate", handlePopstate);
}

function closeModal() {
  window.history.back();
}

onMounted(async () => {
  await fetchCertificates();
});

onUnmounted(() => {
  window.removeEventListener("popstate", handlePopstate);
});
</script>

<template>
  <section v-if="!loading && certificates.length > 0" class="py-20 px-4 md:px-10 bg-white">
    <div class="max-w-6xl mx-auto">
      <div class="text-center mb-12">
        <h2
          class="text-4xl font-black text-black mb-6 font-serif uppercase tracking-wider inline-block relative border-b-4 border-black pb-2">
          <span class="relative z-10">Featured Certificates</span>
          <span class="absolute top-0 left-0 w-full h-full bg-[#E7E7E7] -z-0 -rotate-2 opacity-50"></span>
        </h2>
        <p class="mt-4 font-mono text-gray-500 text-sm md:text-base lowercase tracking-tight max-w-xl mx-auto">
          validated competence. marking the technical milestones of my continuous learning journey.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="cert in featuredCertificates"
          :key="cert.id"
          class="flex flex-col p-4 bg-white rounded-xl border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all duration-200">
          <div
            class="w-full aspect-video bg-gray-100 border-2 border-black rounded-lg mb-4 overflow-hidden relative group flex items-center justify-center p-2">
            <img
              :src="cert.image_url"
              :alt="cert.title"
              class="w-full h-full object-contain transition-all duration-300 group-hover:scale-105" />
          </div>

          <h3 class="text-lg font-bold font-serif leading-tight mb-1">
            {{ cert.title }}
          </h3>
          <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Issued by: {{ cert.issuer }}</p>

          <div class="flex-grow mb-4">
            <p class="text-sm text-gray-600 line-clamp-3 leading-relaxed">
              {{ cert.description }}
            </p>
            <button
              @click="openModal(cert)"
              class="text-xs font-bold text-black underline mt-1 hover:text-gray-600 transition-colors cursor-pointer">
              Read more...
            </button>
          </div>

          <div class="mt-auto pt-3 border-t-2 border-dashed border-gray-200">
            <a
              v-if="cert.credential_link"
              :href="cert.credential_link"
              target="_blank"
              class="flex items-center justify-center gap-1 w-full py-2 text-xs font-bold border-2 border-black rounded bg-white hover:bg-black hover:text-white transition-colors">
              <Icon icon="mdi:certificate" class="text-base" />
              View Credential
            </a>
          </div>
        </div>
      </div>

      <div class="mt-12 flex justify-center">
        <router-link
          to="/certificates"
          class="inline-flex items-center gap-2 px-8 py-3 bg-black text-white border-2 border-black font-black uppercase tracking-wider text-sm shadow-[4px_4px_0px_0px_#9CA3AF] hover:bg-white hover:text-black hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-1 transition-all">
          View All Certificates
          <Icon icon="lucide:arrow-right" class="text-lg" />
        </router-link>
      </div>
    </div>

    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeModal"></div>

      <div
        class="relative bg-white w-full max-w-2xl max-h-[85vh] flex flex-col rounded-xl border-4 border-black shadow-[8px_8px_0px_0px_rgba(255,255,255,1)] animate-in fade-in zoom-in duration-200">
        <div class="flex justify-between items-start p-6 border-b-2 border-black bg-gray-50 rounded-t-lg shrink-0">
          <div>
            <h3 class="text-2xl font-black font-serif uppercase pr-4 leading-none mb-2">
              {{ selectedCert?.title }}
            </h3>
            <span class="text-sm font-bold bg-[#E7E7E7] px-2 py-1 border-2 border-black rounded-sm">
              {{ selectedCert?.issuer }}
            </span>
          </div>

          <button
            @click="closeModal"
            class="hidden md:block p-1 bg-red-500 border-2 border-black text-white hover:bg-red-600 transition-colors rounded-full shrink-0">
            <Icon icon="mdi:close" class="text-xl" />
          </button>
        </div>

        <div class="p-6 overflow-y-auto custom-scrollbar">
          <div
            class="w-full aspect-video bg-gray-100 border-2 border-black rounded-lg mb-6 overflow-hidden flex-shrink-0 flex items-center justify-center p-4">
            <img :src="selectedCert?.image_url" :alt="selectedCert?.title" class="w-full h-full object-contain" />
          </div>

          <div class="prose prose-sm max-w-none text-gray-800 font-medium leading-relaxed whitespace-pre-line">
            {{ selectedCert?.description }}
          </div>
        </div>

        <div class="p-6 border-t-2 border-black bg-gray-50 rounded-b-lg shrink-0">
          <div class="flex flex-col gap-3">
            <a
              v-if="selectedCert?.credential_link"
              :href="selectedCert?.credential_link"
              target="_blank"
              class="flex items-center justify-center gap-2 w-full py-3 text-sm font-bold border-2 border-black rounded bg-white hover:bg-black hover:text-white transition-colors">
              <Icon icon="mdi:certificate-outline" class="text-xl" />
              Verify Credential
            </a>

            <button
              @click="closeModal"
              class="w-full py-3 text-sm font-bold uppercase tracking-wider text-white bg-red-500 border-2 border-black rounded hover:bg-red-600 transition-colors flex items-center justify-center gap-2">
              <Icon icon="mdi:close-circle-outline" class="text-xl" />
              Close Details
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
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
  border-radius: 0;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #333;
}
</style>
