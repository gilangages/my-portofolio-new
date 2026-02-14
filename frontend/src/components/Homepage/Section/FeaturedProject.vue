<script setup>
import { onUnmounted, ref, computed } from "vue"; // 1. Tambah import 'computed'
// ;
import { Icon } from "@iconify/vue";

// State untuk Modal
const isModalOpen = ref(false);
const selectedProject = ref(null);

const props = defineProps({
  projects: {
    type: Array,
    required: true,
    default: () => [], // Fallback agar tidak error jika null
  },
});

// --- NEW LOGIC: FEATURED PROJECTS (LIMIT 3) ---
// Best Practice: Gunakan computed untuk memanipulasi tampilan data tanpa mengubah data aslinya
const featuredProjects = computed(() => {
  return (props.projects || []).slice(0, 3);
});

// --- LOGIC MODAL & BACK BUTTON ---

function openModal(project) {
  selectedProject.value = project;
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
    selectedProject.value = null;
  }, 200);
  window.removeEventListener("popstate", handlePopstate);
}

function closeModal() {
  window.history.back();
}

// onMounted(async () => {
//   await fetchProjects();
// });

onUnmounted(() => {
  window.removeEventListener("popstate", handlePopstate);
});
</script>

<template>
  <section v-if="props.projects && props.projects.length > 0" class="py-20 px-4 md:px-10 bg-white">
    <div class="max-w-6xl mx-auto">
      <div class="text-center mb-12">
        <h2
          class="text-4xl font-black text-black mb-6 font-serif uppercase tracking-wider inline-block relative border-b-4 border-black pb-2">
          <span class="relative z-10">Featured Projects</span>
          <span class="absolute top-0 left-0 w-full h-full bg-[#E7E7E7] -z-0 -rotate-2 opacity-50"></span>
        </h2>
        <p class="mt-4 font-[Inter] text-gray-500 text-sm md:text-base lowercase tracking-tight max-w-xl mx-auto">
          selected works. engineering abstract concepts into scalable production systems.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="project in featuredProjects"
          :key="project.id"
          class="flex flex-col p-4 bg-white rounded-xl border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all duration-200">
          <div
            class="w-full aspect-video bg-gray-100 border-2 border-black rounded-lg mb-4 overflow-hidden relative group">
            <img
              :src="project.thumbnail_url"
              :alt="project.title"
              class="w-full h-full object-cover transition-all duration-300" />
          </div>

          <h3 class="text-lg font-bold font-serif leading-tight mb-3">
            {{ project.title }}
          </h3>

          <div v-if="project.skills && project.skills.length > 0" class="flex flex-wrap gap-2 mb-4">
            <div
              v-for="skill in project.skills"
              :key="skill.id"
              class="px-2 py-1 border border-black rounded-md bg-[#E7E7E7] flex items-center gap-1.5 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-transform cursor-default"
              :title="skill.name">
              <Icon :icon="skill.identifier" class="w-3.5 h-3.5 text-black" />
              <span class="text-[10px] font-bold uppercase tracking-wide leading-none">{{ skill.name }}</span>
            </div>
          </div>

          <div class="flex-grow mb-4">
            <p class="text-sm text-gray-600 line-clamp-3 leading-relaxed">
              {{ project.description }}
            </p>
            <button
              @click="openModal(project)"
              class="text-xs font-bold text-black underline mt-1 hover:text-gray-600 transition-colors cursor-pointer">
              Read more...
            </button>
          </div>

          <div class="flex gap-3 mt-auto pt-3 border-t-2 border-dashed border-gray-200">
            <a
              v-if="project.repository_link"
              :href="project.repository_link"
              target="_blank"
              class="flex-1 flex items-center justify-center gap-1 py-2 text-xs font-bold border-2 border-black rounded bg-white hover:bg-black hover:text-white transition-colors">
              <Icon icon="mdi:github" class="text-base" />
              Code
            </a>

            <a
              v-if="project.live_demo_link"
              :href="project.live_demo_link"
              target="_blank"
              class="flex-1 flex items-center justify-center gap-1 py-2 text-xs font-bold border-2 border-black rounded bg-[#E7E7E7] hover:bg-[#cac9c9] text-black transition-colors">
              <Icon icon="mdi:external-link" class="text-base" />
              Demo
            </a>
          </div>
        </div>
      </div>

      <div class="mt-12 flex justify-center">
        <router-link
          to="/projects"
          class="inline-flex items-center gap-2 px-8 py-3 bg-black text-white border-2 border-black font-black uppercase tracking-wider text-sm shadow-[4px_4px_0px_0px_#9CA3AF] hover:bg-white hover:text-black hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-1 transition-all">
          View All Projects
          <Icon icon="lucide:arrow-right" class="text-lg" />
        </router-link>
      </div>
    </div>

    <Teleport to="body">
      <div v-if="isModalOpen" class="fixed inset-0 z-51 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeModal"></div>

        <div
          class="relative bg-white w-full max-w-2xl max-h-[85vh] flex flex-col rounded-xl border-4 border-black shadow-[8px_8px_0px_0px_rgba(255,255,255,1)] animate-in fade-in zoom-in duration-200">
          <div class="flex justify-between items-start p-6 border-b-2 border-black bg-gray-50 rounded-t-lg shrink-0">
            <h3 class="text-2xl font-black font-serif uppercase pr-4 leading-none">
              {{ selectedProject?.title }}
            </h3>

            <button
              @click="closeModal"
              class="hidden md:block p-1 bg-red-500 border-2 border-black text-white hover:bg-red-600 transition-colors rounded-full shrink-0">
              <Icon icon="mdi:close" class="text-xl" />
            </button>
          </div>

          <div class="p-6 overflow-y-auto custom-scrollbar">
            <div
              class="w-full aspect-video bg-gray-100 border-2 border-black rounded-lg mb-6 overflow-hidden flex-shrink-0">
              <img
                :src="selectedProject?.thumbnail_url"
                :alt="selectedProject?.title"
                class="w-full h-full object-cover" />
            </div>

            <div v-if="selectedProject?.skills && selectedProject.skills.length > 0" class="mb-6">
              <h4 class="font-bold font-serif uppercase text-sm mb-3 border-b-2 border-black inline-block">
                Tech Stack
              </h4>
              <div class="flex flex-wrap gap-2">
                <div
                  v-for="skill in selectedProject.skills"
                  :key="skill.id"
                  class="flex items-center gap-2 px-3 py-1.5 border-2 border-black rounded-md bg-[#E7E7E7] shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] text-xs font-bold uppercase transition-transform hover:-translate-y-0.5 cursor-default">
                  <Icon :icon="skill.identifier" class="text-base" />
                  {{ skill.name }}
                </div>
              </div>
            </div>

            <div class="prose prose-sm max-w-none text-gray-800 font-medium leading-relaxed whitespace-pre-line">
              {{ selectedProject?.description }}
            </div>
          </div>

          <div class="p-6 border-t-2 border-black bg-gray-50 rounded-b-lg shrink-0">
            <div class="flex flex-col gap-3">
              <div class="flex gap-3">
                <a
                  v-if="selectedProject?.repository_link"
                  :href="selectedProject?.repository_link"
                  target="_blank"
                  class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold border-2 border-black rounded bg-white hover:bg-black hover:text-white transition-colors">
                  <Icon icon="mdi:github" class="text-xl" />
                  View Code
                </a>
                <a
                  v-if="selectedProject?.live_demo_link"
                  :href="selectedProject?.live_demo_link"
                  target="_blank"
                  class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-bold border-2 border-black rounded bg-[#E7E7E7] hover:bg-[#cac9c9] text-black transition-colors">
                  <Icon icon="mdi:external-link" class="text-xl" />
                  Live Demo
                </a>
              </div>

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
    </Teleport>
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
