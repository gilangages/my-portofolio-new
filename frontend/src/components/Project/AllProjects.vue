<script setup>
import { onMounted, ref, nextTick, watch } from "vue";
import LoadingScreen from "../LoadingScreen.vue";
import { alertError } from "../../lib/alert";
import { getAllProjects } from "../../lib/api/ProjectApi";
import { Icon } from "@iconify/vue";
import gsap from "gsap";
import { marked } from "marked";

const projects = ref([]);
const loading = ref(true);

// State untuk Modal Detail
const isModalOpen = ref(false);
const selectedProject = ref(null);
const renderMarkdown = (text) => {
  if (!text) return "";
  return marked.parse(text, { breaks: true });
};

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
      window.dispatchEvent(new CustomEvent("content-loaded"));
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
    <div class="px-4 py-16 md:px-8 max-w-6xl mx-auto">
      <Transition name="fade">
        <LoadingScreen v-if="loading" />
      </Transition>

      <div v-if="!loading">
        <div class="text-center mb-12 -mt-12 md:mt-8 page-title" style="opacity: 0; visibility: hidden">
          <h1
            class="text-3xl md:text-5xl font-black font-serif uppercase tracking-wider inline-block relative border-b-4 border-black pb-2">
            <span class="relative z-10">All Projects</span>
            <span class="absolute top-0 left-0 w-full h-full bg-gray-200 -z-0 -rotate-1 skew-x-12 opacity-70"></span>
          </h1>
          <p class="mt-4 font-[Inter] text-gray-600 text-sm max-w-xl mx-auto italic">
            "A collection of crafted code, deployed solutions, and creative experiments."
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-20">
          <div
            v-for="project in projects"
            :key="project.id"
            class="project-card group flex flex-col bg-white border-2 border-black rounded-lg p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[2px] hover:translate-y-[2px] transition-all duration-200"
            style="opacity: 0; visibility: hidden">
            <div
              class="w-full aspect-video bg-gray-50 border-2 border-black rounded-md mb-4 overflow-hidden relative flex items-center justify-center">
              <img
                v-if="project.thumbnail_url"
                :src="project.thumbnail_url"
                :alt="project.title"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
              <div v-else class="flex flex-col items-center justify-center text-gray-400">
                <Icon icon="mdi:image-off-outline" class="text-3xl mb-2" />
                <span class="text-[10px] font-bold uppercase">No Preview</span>
              </div>
            </div>

            <div class="flex flex-col flex-grow">
              <h3
                class="text-xl md:text-2xl font-black font-serif leading-tight mb-2 group-hover:underline decoration-2 underline-offset-4 decoration-black">
                {{ project.title }}
              </h3>

              <div class="flex flex-wrap gap-1.5 mb-3">
                <span
                  v-for="skill in project.skills?.slice(0, 3)"
                  :key="skill.id || skill"
                  class="inline-block px-1.5 py-0.5 text-[10px] font-bold bg-gray-100 border border-black rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] uppercase">
                  {{ skill.name || skill }}
                </span>
                <span v-if="project.skills?.length > 3" class="text-[10px] font-bold self-center">
                  +{{ project.skills.length - 3 }}
                </span>
              </div>

              <div
                v-html="renderMarkdown(project.description)"
                class="markdown-preview text-xs md:text-sm text-gray-600 line-clamp-3 mb-4 font-medium border-l-2 border-gray-300 pl-2"></div>

              <div class="mt-auto pt-3 border-t-2 border-dashed border-gray-300">
                <button
                  @click="openModal(project)"
                  class="w-full py-1.5 px-3 text-xs md:text-sm font-bold uppercase border-2 border-black rounded bg-white hover:bg-black hover:text-white transition-all flex items-center justify-center gap-2">
                  <Icon icon="mdi:eye-outline" class="text-base" />
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
          class="relative bg-white w-full max-w-2xl max-h-[90vh] flex flex-col rounded-lg border-2 border-black shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] animate-in fade-in zoom-in duration-200">
          <div
            class="flex justify-between items-start p-4 md:p-5 border-b-2 border-black bg-gray-100 rounded-t-md shrink-0">
            <div>
              <h3 class="text-xl md:text-3xl font-black font-serif uppercase leading-none mb-1.5">
                {{ selectedProject?.title }}
              </h3>
              <div class="flex flex-wrap gap-1.5 mt-2">
                <span
                  v-for="skill in selectedProject?.skills"
                  :key="skill.id || skill"
                  class="px-1.5 py-0.5 text-[10px] md:text-xs font-bold bg-white border border-black rounded shadow-[1px_1px_0px_0px_black] uppercase">
                  {{ skill.name || skill }}
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
            <div
              class="w-full aspect-video bg-gray-50 border-2 border-black rounded-md mb-4 md:mb-5 overflow-hidden relative shadow-[2px_2px_0px_0px_rgba(0,0,0,0.2)]">
              <img
                v-if="selectedProject?.thumbnail_url"
                :src="selectedProject?.thumbnail_url"
                :alt="selectedProject?.title"
                class="w-full h-full object-cover" />
              <div v-else class="flex w-full h-full items-center justify-center text-gray-400">
                <Icon icon="mdi:image-off-outline" class="text-4xl" />
              </div>
            </div>

            <div
              v-html="renderMarkdown(selectedProject?.description)"
              class="markdown-preview font-mono text-sm md:text-base text-gray-700 leading-relaxed"></div>
          </div>

          <div
            class="p-4 md:p-5 border-t-2 border-black bg-gray-100 rounded-b-md shrink-0 flex flex-col sm:flex-row gap-2.5">
            <a
              v-if="selectedProject?.live_demo_link"
              :href="selectedProject?.live_demo_link"
              target="_blank"
              class="flex-1 flex items-center justify-center gap-2 py-2 text-xs md:text-sm font-bold border-2 border-black rounded bg-[#2ecc71] text-white hover:bg-[#27ae60] transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px]">
              <Icon icon="mdi:web" class="text-lg" />
              Live Demo
            </a>

            <a
              v-if="selectedProject?.repository_link"
              :href="selectedProject?.repository_link"
              target="_blank"
              class="flex-1 flex items-center justify-center gap-2 py-2 text-xs md:text-sm font-bold border-2 border-black rounded bg-white hover:bg-black hover:text-white transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px]">
              <Icon icon="mdi:github" class="text-lg" />
              Repository
            </a>

            <button
              @click="closeModal"
              class="flex-1 flex items-center justify-center gap-2 py-2 text-xs md:text-sm font-bold text-white bg-red-600 border-2 border-black rounded hover:bg-red-700 transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px]">
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

/* Custom Scrollbar untuk Modal - Sedikit dirampingkan */
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
