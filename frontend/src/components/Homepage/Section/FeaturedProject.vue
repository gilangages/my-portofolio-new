<script setup>
import { onUnmounted, ref, computed } from "vue"; // 1. Tambah import 'computed'
// ;
import { Icon } from "@iconify/vue";
import { marked } from "marked";

// State untuk Modal
const isModalOpen = ref(false);
const selectedProject = ref(null);
const renderMarkdown = (text) => {
  if (!text) return "";
  return marked.parse(text, { breaks: true });
};

// Helper: Format enum value ke label yang readable
const formatLabel = (value) => {
  if (!value) return "";
  return value.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
};

// Helper: Format date ke "Jan 2025"
const formatDate = (dateStr) => {
  if (!dateStr) return "";
  const d = new Date(dateStr);
  return d.toLocaleDateString("en-US", { month: "short", year: "numeric" });
};

// Helper: Status badge color class
const statusClass = (status) => {
  const map = {
    completed: "bg-green-100 text-green-800 border-green-600",
    in_development: "bg-yellow-100 text-yellow-800 border-yellow-600",
    on_hold: "bg-gray-100 text-gray-700 border-gray-500",
    cancelled: "bg-red-100 text-red-800 border-red-600",
  };
  return map[status] || "bg-gray-100 text-gray-700 border-gray-500";
};

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

          <h3 class="text-lg font-bold font-serif leading-tight mb-2">
            {{ project.title }}
          </h3>

          <div class="flex flex-wrap gap-1.5 mb-3">
            <span
              v-if="project.status"
              class="text-[10px] font-bold uppercase px-1.5 py-0.5 border rounded-sm"
              :class="statusClass(project.status)">
              {{ formatLabel(project.status) }}
            </span>
            <span
              v-if="project.type"
              class="text-[10px] font-bold uppercase px-1.5 py-0.5 border border-black rounded-sm bg-[#E7E7E7]">
              {{ formatLabel(project.type) }}
            </span>
          </div>

          <div v-if="project.start_date" class="flex items-center gap-1 text-[10px] text-gray-500 font-mono mb-3">
            <Icon icon="lucide:calendar" class="w-3 h-3" />
            {{ formatDate(project.start_date) }} → {{ formatDate(project.end_date) }}
          </div>

          <div v-if="project.skills && project.skills.length > 0" class="flex flex-wrap gap-2 mb-4">
            <div
              v-for="skill in project.skills.slice(0, 3)"
              :key="skill.id"
              class="px-2 py-1 border border-black rounded-md bg-[#E7E7E7] flex items-center gap-1.5 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 transition-transform cursor-default"
              :title="skill.name">
              <Icon :icon="skill.identifier" class="w-3.5 h-3.5 text-black" />
              <span class="text-[10px] font-bold uppercase tracking-wide leading-none">{{ skill.name }}</span>
            </div>
            <!-- Indicator +X if skills > 3 -->
            <div
              v-if="project.skills.length > 3"
              class="px-2 py-1 border border-black rounded-md bg-black text-white flex items-center justify-center shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] cursor-default"
              :title="project.skills.slice(3).map(s => s.name).join(', ')">
              <span class="text-[10px] font-bold uppercase tracking-wide leading-none">+{{ project.skills.length - 3 }}</span>
            </div>
          </div>

          <div class="flex-grow mb-4">
            <div
              v-html="renderMarkdown(project.description)"
              class="markdown-preview text-xs md:text-sm text-gray-600 line-clamp-3 mb-4 font-medium border-l-2 border-gray-300 pl-2"></div>
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

            <div class="flex flex-wrap gap-2 mb-4">
              <span
                v-if="selectedProject?.status"
                class="text-xs font-bold uppercase px-2 py-1 border-2 rounded-sm"
                :class="statusClass(selectedProject.status)">
                {{ formatLabel(selectedProject.status) }}
              </span>
              <span
                v-if="selectedProject?.type"
                class="text-xs font-bold uppercase px-2 py-1 border-2 border-black rounded-sm bg-[#E7E7E7]">
                {{ formatLabel(selectedProject.type) }}
              </span>
              <span
                v-if="selectedProject?.start_date"
                class="text-xs font-mono text-gray-500 flex items-center gap-1 px-2 py-1">
                <Icon icon="lucide:calendar" class="w-3.5 h-3.5" />
                {{ formatDate(selectedProject.start_date) }} → {{ formatDate(selectedProject.end_date) }}
              </span>
            </div>

            <div v-if="selectedProject?.role || selectedProject?.team_size" class="mb-6 p-4 border-2 border-black bg-gray-50 flex flex-wrap gap-6 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
              <div v-if="selectedProject?.role">
                <h4 class="text-[10px] font-black uppercase text-gray-400 mb-1">Role:</h4>
                <div class="flex items-center gap-2 font-bold text-sm uppercase">
                  <Icon icon="lucide:user-cog" class="text-lg" />
                  {{ selectedProject.role }}
                </div>
              </div>
              <div v-if="selectedProject?.team_size">
                <h4 class="text-[10px] font-black uppercase text-gray-400 mb-1">Team Size:</h4>
                <div class="flex items-center gap-2 font-bold text-sm uppercase">
                  <Icon icon="lucide:users" class="text-lg" />
                  {{ selectedProject.team_size }} {{ selectedProject.team_size > 1 ? 'People' : 'Person' }}
                </div>
              </div>
            </div>

            <div
              v-html="renderMarkdown(selectedProject?.description)"
              class="markdown-preview font-mono text-sm md:text-base text-gray-700 leading-relaxed"></div>
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
