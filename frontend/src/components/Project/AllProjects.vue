<script setup>
import { onMounted, ref, nextTick, watch } from "vue";
import LoadingScreen from "../LoadingScreen.vue";
import { alertError } from "../lib/alert";
import { getAllProjects } from "../lib/api/ProjectApi";
import { Icon } from "@iconify/vue";
import gsap from "gsap";

const projects = ref([]);
const loading = ref(true);

// State untuk Modal Detail
const isModalOpen = ref(false);
const selectedProject = ref(null);

// --- FUNCTION FETCH DATA (Dengan Delay Buatan agar Smooth) ---
async function fetchProjects() {
  loading.value = true;
  try {
    const response = await getAllProjects();
    const responseBody = await response.json();

    if (response.status === 200) {
      projects.value = responseBody.data || responseBody;
    } else {
      await alertError(responseBody.message);
    }
  } catch (e) {
    console.error(`Error fetch projects:`, e);
  } finally {
    // Delay 800ms sebelum loading hilang agar transisi tidak kasar
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

      // 2. Animasi Kartu Project
      if (projects.value.length > 0) {
        tl.fromTo(
          ".project-card",
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
function openModal(project) {
  selectedProject.value = project;
  isModalOpen.value = true;
  document.body.style.overflow = "hidden";
}

function closeModal() {
  isModalOpen.value = false;
  document.body.style.overflow = "auto";
  setTimeout(() => {
    selectedProject.value = null;
  }, 200);
}

onMounted(async () => {
  await fetchProjects();
});
</script>

<template>
  <div class="min-h-screen bg-white">
    <div class="px-4 py-18 md:px-10 max-w-7xl mx-auto">
      <Transition name="fade">
        <LoadingScreen v-if="loading" />
      </Transition>

      <div v-if="!loading">
        <div class="text-center mb-16 -mt-14 md:mt-8 page-title" style="opacity: 0; visibility: hidden">
          <h1
            class="text-4xl md:text-6xl font-black font-serif uppercase tracking-wider inline-block relative border-b-8 border-black pb-2">
            <span class="relative z-10">All Projects</span>
            <span class="absolute top-0 left-0 w-full h-full bg-gray-200 -z-0 -rotate-1 skew-x-12 opacity-70"></span>
          </h1>
          <p class="mt-6 font-[Inter] text-gray-600 text-sm md:text-base max-w-2xl mx-auto italic">
            "A collection of crafted code, deployed solutions, and creative experiments."
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
          <div
            v-for="project in projects"
            :key="project.id"
            class="project-card group flex flex-col bg-white border-4 border-black rounded-xl p-4 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[4px] hover:translate-y-[4px] transition-all duration-200"
            style="opacity: 0; visibility: hidden">
            <div
              class="w-full aspect-video bg-gray-50 border-2 border-black rounded-lg mb-5 overflow-hidden relative flex items-center justify-center">
              <img
                v-if="project.thumbnail_url"
                :src="project.thumbnail_url"
                :alt="project.title"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
              <div v-else class="flex flex-col items-center justify-center text-gray-400">
                <Icon icon="mdi:image-off-outline" class="text-4xl mb-2" />
                <span class="text-xs font-bold uppercase">No Preview</span>
              </div>
            </div>

            <div class="flex flex-col flex-grow">
              <h3
                class="text-2xl font-black font-serif leading-tight mb-3 group-hover:underline decoration-4 underline-offset-4 decoration-black">
                {{ project.title }}
              </h3>

              <div class="flex flex-wrap gap-2 mb-4">
                <span
                  v-for="skill in project.skills?.slice(0, 3)"
                  :key="skill.id || skill"
                  class="inline-block px-2 py-1 text-[10px] font-bold bg-gray-100 border border-black rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase">
                  {{ skill.name || skill }}
                </span>
                <span v-if="project.skills?.length > 3" class="text-xs font-bold self-center">
                  +{{ project.skills.length - 3 }}
                </span>
              </div>

              <p class="text-sm text-gray-600 line-clamp-3 mb-6 font-medium border-l-2 border-gray-300 pl-3">
                {{ project.description }}
              </p>

              <div class="mt-auto grid grid-cols-2 gap-3 pt-4 border-t-2 border-dashed border-gray-300">
                <button
                  @click="openModal(project)"
                  class="col-span-2 py-2 px-3 text-sm font-bold uppercase border-2 border-black rounded bg-white hover:bg-black hover:text-white transition-all flex items-center justify-center gap-2">
                  <Icon icon="mdi:eye-outline" class="text-lg" />
                  View Details
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Teleport to="body">
      <div v-if="isModalOpen" class="fixed inset-0 z-[999] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeModal"></div>

        <div
          class="relative bg-white w-full max-w-3xl max-h-[90vh] flex flex-col rounded-xl border-4 border-black shadow-[12px_12px_0px_0px_rgba(255,255,255,1)] animate-in fade-in zoom-in duration-200">
          <div class="flex justify-between items-start p-6 border-b-4 border-black bg-gray-100 rounded-t-lg shrink-0">
            <div>
              <h3 class="text-2xl md:text-4xl font-black font-serif uppercase leading-none mb-2">
                {{ selectedProject?.title }}
              </h3>
              <div class="flex flex-wrap gap-2 mt-2">
                <span
                  v-for="skill in selectedProject?.skills"
                  :key="skill.id || skill"
                  class="px-2 py-1 text-xs font-bold bg-white border border-black rounded shadow-[1px_1px_0px_0px_black] uppercase">
                  {{ skill.name || skill }}
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
            <div
              class="w-full aspect-video bg-gray-50 border-2 border-black rounded-lg mb-6 overflow-hidden relative shadow-[4px_4px_0px_0px_rgba(0,0,0,0.2)]">
              <img
                v-if="selectedProject?.thumbnail_url"
                :src="selectedProject?.thumbnail_url"
                :alt="selectedProject?.title"
                class="w-full h-full object-cover" />
              <div v-else class="flex w-full h-full items-center justify-center text-gray-400">
                <Icon icon="mdi:image-off-outline" class="text-6xl" />
              </div>
            </div>

            <div
              class="prose prose-sm md:prose-lg max-w-none text-gray-800 font-medium leading-relaxed whitespace-pre-line border-l-4 border-black pl-5">
              {{ selectedProject?.description }}
            </div>
          </div>

          <div class="p-6 border-t-4 border-black bg-gray-100 rounded-b-lg shrink-0 flex flex-col md:flex-row gap-3">
            <a
              v-if="selectedProject?.live_demo_link"
              :href="selectedProject?.live_demo_link"
              target="_blank"
              class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold border-2 border-black rounded bg-[#2ecc71] text-white hover:bg-[#27ae60] hover:text-white transition-all shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px]">
              <Icon icon="mdi:web" class="text-xl" />
              Live Demo
            </a>

            <a
              v-if="selectedProject?.repository_link"
              :href="selectedProject?.repository_link"
              target="_blank"
              class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold border-2 border-black rounded bg-white hover:bg-black hover:text-white transition-all shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px]">
              <Icon icon="mdi:github" class="text-xl" />
              Repository
            </a>

            <button
              @click="closeModal"
              class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold text-white bg-red-600 border-2 border-black rounded hover:bg-red-700 transition-all shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px]">
              Close
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
/* Tambahan Style Transisi */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.6s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

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
