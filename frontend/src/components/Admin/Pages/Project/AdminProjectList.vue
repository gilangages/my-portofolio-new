<script setup>
import { ref, onMounted } from "vue";
import { useLocalStorage } from "@vueuse/core";
import { Icon } from "@iconify/vue";
import { getAllProjects, adminDeleteProject } from "../../../lib/api/ProjectApi";
import { alertSuccess, alertError, alertConfirmProject } from "../../../lib/alert";

const projects = ref([]);
const isLoading = ref(true);
const token = useLocalStorage("token", "");
const storageUrl = import.meta.env.VITE_STORAGE_URL;

const getFullUrl = (path) => {
  if (!path) return "";
  if (path.startsWith("http://") || path.startsWith("https://")) {
    return path;
  }
  return `${storageUrl}${path}`;
};

const fetchData = async () => {
  isLoading.value = true;
  try {
    const response = await getAllProjects();
    const result = await response.json();
    projects.value = result.data || result;
  } catch (error) {
    console.error(error);
    alertError("Gagal mengambil data project.");
  } finally {
    isLoading.value = false;
  }
};

const handleDelete = async (id) => {
  const isConfirmed = await alertConfirmProject("Yakin ingin menghapus project ini?");
  if (!isConfirmed) return;

  try {
    const response = await adminDeleteProject(token.value, id);
    if (response.ok) {
      await alertSuccess("Project berhasil dihapus! 🗑️");
      fetchData();
    } else {
      await alertError("Gagal menghapus data.");
    }
  } catch (error) {
    alertError("Terjadi kesalahan sistem.");
  }
};

onMounted(() => {
  fetchData();
});
</script>

<template>
  <div class="p-6 max-w-7xl mx-auto">
    <div
      class="mb-10 border-b-4 border-black pb-4 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
      <div>
        <h1 class="text-3xl md:text-4xl font-black italic uppercase">MANAGE PROJECTS</h1>
        <p class="font-mono text-gray-600 mt-2">Edit or remove your masterpieces.</p>
      </div>

      <router-link
        v-if="!isLoading && projects.length > 0"
        to="/admin/dashboard/projects/create"
        class="bg-green-400 text-black border-2 border-black px-4 py-2 font-bold uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-y-[4px] active:shadow-none transition-all flex items-center gap-2">
        <Icon icon="lucide:plus" class="text-xl" />
        <span>Add New</span>
      </router-link>
    </div>

    <div v-if="isLoading" class="p-8 text-center font-mono animate-pulse border-4 border-black bg-white">
      LOADING PROJECTS...
    </div>

    <div
      v-else-if="projects.length === 0"
      class="p-12 text-center border-4 border-black bg-white shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] flex flex-col items-center gap-4">
      <div class="bg-gray-100 p-4 rounded-full border-2 border-black">
        <Icon icon="lucide:rocket" class="text-4xl text-gray-400" />
      </div>

      <div>
        <h3 class="font-bold text-xl uppercase mb-1">No Projects Found</h3>
        <p class="font-mono text-gray-500 mb-6">You haven't launched any projects yet.</p>

        <router-link
          to="/admin/dashboard/projects/create"
          class="inline-flex flex-col items-center justify-center gap-1 bg-green-400 text-black border-2 border-black px-5 py-2 font-bold uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:scale-105 transition-transform text-sm">
          <Icon icon="lucide:plus-circle" class="text-xl" />
          <span>Launch Project!</span>
        </router-link>
      </div>
    </div>

    <div v-else>
      <div class="hidden md:block border-4 border-black bg-white shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
        <table class="w-full text-left border-collapse">
          <thead class="bg-black text-white font-mono uppercase text-sm">
            <tr>
              <th class="p-4 border-r-2 border-white w-24">Thumbnail</th>
              <th class="p-4 border-r-2 border-white">Project Info</th>
              <th class="p-4 border-r-2 border-white w-1/3">Tech Stack</th>
              <th class="p-4 text-center w-32">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="project in projects"
              :key="project.id"
              class="border-b-2 border-black hover:bg-yellow-50 transition-colors">
              <td class="p-4 align-top">
                <img
                  :src="getFullUrl(project.thumbnail_path)"
                  class="w-24 h-16 object-cover border-2 border-black shadow-sm" />
              </td>

              <td class="p-4 align-top">
                <div class="max-w-[250px] lg:max-w-[400px]">
                  <div class="font-bold uppercase text-lg leading-tight mb-1">{{ project.title }}</div>
                  <p class="text-sm text-gray-500 font-mono mb-2 line-clamp-2">
                    {{ project.description }}
                  </p>
                  <div class="flex gap-3 mt-2">
                    <a
                      v-if="project.repository_link"
                      :href="project.repository_link"
                      target="_blank"
                      class="text-gray-600 hover:text-black hover:scale-110 transition-transform">
                      <Icon icon="mdi:github" class="w-6 h-6" />
                    </a>
                    <a
                      v-if="project.live_demo_link"
                      :href="project.live_demo_link"
                      target="_blank"
                      class="text-gray-600 hover:text-blue-600 hover:scale-110 transition-transform">
                      <Icon icon="mdi:web" class="w-6 h-6" />
                    </a>
                  </div>
                </div>
              </td>

              <td class="p-4 align-top">
                <div v-if="project.skills && project.skills.length" class="flex flex-wrap gap-2">
                  <span
                    v-for="tech in project.skills"
                    :key="tech.id"
                    class="text-xs border border-black px-2 py-1 bg-white flex items-center gap-1 font-mono">
                    <Icon :icon="tech.identifier || 'lucide:code'" class="w-4 h-4" />
                    {{ tech.name }}
                  </span>
                </div>
              </td>
              <td class="p-4 text-center align-middle">
                <div class="flex justify-center gap-2">
                  <router-link
                    :to="`/admin/dashboard/projects/edit/${project.id}`"
                    class="bg-yellow-300 hover:bg-yellow-500 p-2 border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:scale-110 transition-transform">
                    <Icon icon="lucide:pencil" class="w-4 h-4" />
                  </router-link>
                  <button
                    @click="handleDelete(project.id)"
                    class="bg-red-400 p-2 border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:scale-110 transition-transform text-white">
                    <Icon icon="lucide:trash-2" class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="md:hidden space-y-6">
        <div
          v-for="project in projects"
          :key="project.id + '-mobile'"
          class="border-4 border-black bg-white p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex flex-col gap-4">
          <div class="flex gap-4 items-start">
            <img
              :src="getFullUrl(project.thumbnail_path)"
              class="w-20 h-20 object-cover border-2 border-black flex-shrink-0 bg-gray-100" />

            <div class="flex-1 min-w-0">
              <h3 class="font-black text-lg uppercase leading-tight break-words">{{ project.title }}</h3>
              <p class="text-xs text-gray-500 font-mono mt-1 line-clamp-3">
                {{ project.description }}
              </p>
              <div class="flex gap-3 mt-3">
                <a
                  v-if="project.repository_link"
                  :href="project.repository_link"
                  target="_blank"
                  class="w-8 h-8 flex items-center justify-center border-2 border-black bg-gray-100 hover:bg-black hover:text-white transition-colors shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-y-[2px] active:shadow-none">
                  <Icon icon="mdi:github" class="w-5 h-5" />
                </a>
                <a
                  v-if="project.live_demo_link"
                  :href="project.live_demo_link"
                  target="_blank"
                  class="w-8 h-8 flex items-center justify-center border-2 border-black bg-gray-100 hover:bg-yellow-300 hover:text-black transition-colors shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-y-[2px] active:shadow-none">
                  <Icon icon="mdi:web" class="w-5 h-5" />
                </a>
              </div>
            </div>
          </div>

          <div class="border-t-2 border-black border-dashed pt-3">
            <p class="text-xs font-bold uppercase mb-2">Tech Stack:</p>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="tech in project.skills"
                :key="tech.id"
                class="text-[10px] border border-black px-1.5 py-0.5 bg-gray-50 flex items-center gap-1 font-mono">
                <Icon :icon="tech.identifier || 'lucide:code'" class="w-3 h-3" />
                {{ tech.name }}
              </span>
              <span v-if="!project.skills?.length" class="text-xs italic text-gray-400">No tech data</span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3 mt-2">
            <router-link
              :to="`/admin/dashboard/projects/edit/${project.id}`"
              class="flex items-center justify-center gap-2 bg-yellow-300 border-2 border-black py-2 font-bold text-sm shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[2px] active:translate-y-[2px] transition-all">
              <Icon icon="lucide:pencil" />
              Edit
            </router-link>
            <button
              @click="handleDelete(project.id)"
              class="flex items-center justify-center gap-2 bg-red-400 text-white border-2 border-black py-2 font-bold text-sm shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[2px] active:translate-y-[2px] transition-all">
              <Icon icon="lucide:trash-2" />
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
