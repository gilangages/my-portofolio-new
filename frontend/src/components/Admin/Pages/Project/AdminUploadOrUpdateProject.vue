<script setup>
import { useLocalStorage } from "@vueuse/core";
import { reactive, ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { Icon } from "@iconify/vue";
import { adminUploadProject, adminUpdateProject, getSingleProject } from "../../../llib/api/ProjectApi";
import { getSkills } from "../../../llib/api/SkillApi";
import { alertSuccess, alertError } from "../../../llib/alert";

const route = useRoute();
const router = useRouter();
const token = useLocalStorage("token", "");

// --- KONFIGURASI URL STORAGE ---
const storageUrl = import.meta.env.VITE_STORAGE_URL || "http://localhost:8000/storage/";

// --- DETEKSI MODE (EDIT ATAU BARU) ---
const projectId = route.params.id;
const isEditMode = computed(() => !!projectId);

const skills = ref([]);
const selectedSkillIds = ref([]);
const isLoading = ref(false);
const isFetchingSkills = ref(true);

const form = reactive({
  title: "",
  description: "",
  repository_link: "",
  live_demo_link: "",
});

const file = ref(null);
const previewImage = ref(null);

// --- FETCH DATA ---
onMounted(async () => {
  try {
    const response = await getSkills();
    const result = await response.json();

    if (Array.isArray(result)) {
      skills.value = result;
    } else if (result.data) {
      skills.value = result.data;
    }
  } catch (error) {
    console.error("Error fetching skills:", error);
    alertError("Gagal memuat skill.");
  } finally {
    isFetchingSkills.value = false;
  }

  if (isEditMode.value) {
    try {
      const response = await getSingleProject(projectId);
      const result = await response.json();
      const data = result.data || result;

      form.title = data.title;
      form.description = data.description;
      form.repository_link = data.repository_link;
      form.live_demo_link = data.live_demo_link;

      if (data.skills && Array.isArray(data.skills)) {
        selectedSkillIds.value = data.skills.map((item) => item.id);
      }

      if (data.thumbnail_path) {
        previewImage.value = `${storageUrl}${data.thumbnail_path}`;
      }
    } catch (error) {
      console.error(error);
      alertError("Gagal mengambil data project.");
      router.push("/admin/dashboard/projects");
    }
  }
});

// --- HANDLE SELECTION ---
const toggleSkill = (id) => {
  if (selectedSkillIds.value.includes(id)) {
    selectedSkillIds.value = selectedSkillIds.value.filter((s) => s !== id);
  } else {
    selectedSkillIds.value.push(id);
  }
};

const handleFileChange = (e) => {
  const selectedFile = e.target.files[0];
  if (selectedFile) {
    file.value = selectedFile;
    previewImage.value = URL.createObjectURL(selectedFile);
  }
};

const removeImage = () => {
  previewImage.value = null;
  file.value = null;
  const input = document.getElementById("file-upload");
  if (input) input.value = "";
};

// --- SUBMIT ---
async function handleSubmit() {
  if (!form.title) {
    alertError("Judul wajib diisi!");
    return;
  }
  if (!isEditMode.value && !file.value) {
    alertError("Thumbnail wajib diisi untuk project baru!");
    return;
  }
  if (selectedSkillIds.value.length === 0) {
    alertError("Pilih minimal satu Tech Stack!");
    return;
  }

  isLoading.value = true;
  try {
    const formData = new FormData();
    formData.append("title", form.title);
    formData.append("description", form.description || "");
    formData.append("repository_link", form.repository_link || "");
    formData.append("live_demo_link", form.live_demo_link || "");

    if (file.value) {
      formData.append("thumbnail", file.value);
    }

    selectedSkillIds.value.forEach((id) => {
      formData.append("tech_stack_ids[]", id);
    });

    let response;
    if (isEditMode.value) {
      formData.append("_method", "PUT");
      response = await adminUpdateProject(token.value, projectId, formData);
    } else {
      response = await adminUploadProject(token.value, formData);
    }

    const responseBody = await response.json();

    if (response.ok || response.status === 201 || response.status === 200) {
      await alertSuccess(isEditMode.value ? "Project berhasil diupdate! 🚀" : "Project berhasil diluncurkan! 🚀");
      router.push("/admin/dashboard/projects");
    } else {
      await alertError(responseBody.message || "Gagal menyimpan project");
    }
  } catch (e) {
    console.error(e);
    alertError("Terjadi kesalahan sistem.");
  } finally {
    isLoading.value = false;
  }
}
</script>

<template>
  <div class="p-6 max-w-7xl mx-auto">
    <div class="mb-8">
      <router-link
        to="/admin/dashboard/projects"
        class="inline-flex items-center gap-2 font-bold font-mono text-sm mb-4 hover:underline hover:text-red-500 transition-colors">
        <Icon icon="lucide:arrow-left" class="text-lg" />
        BACK TO LIST
      </router-link>

      <div class="border-b-4 border-black pb-4 flex justify-between items-end">
        <div>
          <h1 class="text-3xl md:text-4xl font-black italic uppercase">
            {{ isEditMode ? "EDIT PROJECT" : "UPLOAD PROJECT" }}
          </h1>
          <p class="font-mono text-gray-600 mt-2 text-sm md:text-base">
            {{ isEditMode ? "Refine your masterpiece." : "Showcase your latest masterpiece." }}
          </p>
        </div>
        <div
          class="hidden md:block bg-black text-white px-3 py-1 font-mono font-bold uppercase transform rotate-2 shadow-[4px_4px_0px_0px_rgba(200,200,200,1)]">
          {{ isEditMode ? "Update Mode" : "Create New" }}
        </div>
      </div>
    </div>

    <div class="border-4 border-black p-6 md:p-8 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] bg-white mb-12">
      <form @submit.prevent="handleSubmit" class="flex flex-col lg:flex-row gap-8">
        <div class="flex-1 space-y-6">
          <div>
            <label class="block font-black mb-2 border-b-2 border-black inline-block text-sm uppercase">
              Project Title
              <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.title"
              type="text"
              placeholder="e.g. THE NEXT BIG APP"
              class="w-full p-4 border-2 border-black font-bold focus:bg-yellow-50 focus:outline-none transition-colors shadow-[4px_4px_0px_0px_rgba(0,0,0,0)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] placeholder:font-normal placeholder:text-gray-400" />
          </div>

          <div>
            <label class="block font-black mb-2 border-b-2 border-black inline-block text-sm uppercase">
              Description
            </label>
            <textarea
              v-model="form.description"
              rows="6"
              placeholder="Explain details about the project..."
              class="w-full p-4 border-2 border-black font-medium focus:bg-yellow-50 focus:outline-none transition-colors shadow-[4px_4px_0px_0px_rgba(0,0,0,0)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] placeholder:font-normal placeholder:text-gray-400 resize-none"></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block font-black mb-2 text-xs uppercase flex items-center gap-2">
                <Icon icon="mdi:github" class="text-lg" />
                Repository
              </label>
              <input
                v-model="form.repository_link"
                type="url"
                placeholder="https://github.com/..."
                class="w-full p-3 border-2 border-black font-mono text-sm focus:bg-yellow-50 focus:outline-none transition-colors shadow-[2px_2px_0px_0px_rgba(0,0,0,0)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]" />
            </div>
            <div>
              <label class="block font-black mb-2 text-xs uppercase flex items-center gap-2">
                <Icon icon="mdi:web" class="text-lg" />
                Live Demo
              </label>
              <input
                v-model="form.live_demo_link"
                type="url"
                placeholder="https://mysite.com"
                class="w-full p-3 border-2 border-black font-mono text-sm focus:bg-yellow-50 focus:outline-none transition-colors shadow-[2px_2px_0px_0px_rgba(0,0,0,0)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]" />
            </div>
          </div>

          <div class="pt-2">
            <label class="block font-black mb-3 border-b-2 border-black inline-block text-sm uppercase">
              Tech Stack
              <span class="text-red-500">*</span>
            </label>

            <div
              v-if="isFetchingSkills"
              class="p-4 border-2 border-black border-dashed bg-gray-50 text-center font-mono animate-pulse">
              LOADING TECH DATA...
            </div>

            <div v-else class="flex flex-wrap gap-3">
              <button
                v-for="skill in skills"
                :key="skill.id"
                type="button"
                @click="toggleSkill(skill.id)"
                class="group relative px-4 py-2 text-sm font-bold border-2 border-black transition-all duration-200 select-none flex items-center gap-2 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 active:translate-y-0 active:shadow-[0px_0px_0px_0px_rgba(0,0,0,1)]"
                :class="
                  selectedSkillIds.includes(skill.id)
                    ? 'bg-black text-white hover:bg-gray-800'
                    : 'bg-white text-black hover:bg-green-50'
                ">
                <Icon
                  :icon="skill.identifier || 'lucide:code'"
                  class="text-lg"
                  :class="
                    selectedSkillIds.includes(skill.id) ? 'text-white' : 'text-gray-700 group-hover:text-black'
                  " />
                <span class="font-mono uppercase">{{ skill.name }}</span>
                <div
                  v-if="selectedSkillIds.includes(skill.id)"
                  class="absolute -top-2 -right-2 bg-green-400 text-black border-2 border-black w-5 h-5 flex items-center justify-center rounded-full text-xs">
                  <Icon icon="lucide:check" stroke-width="4" />
                </div>
              </button>
            </div>
            <p class="font-mono text-xs text-gray-500 mt-3 text-right">{{ selectedSkillIds.length }} tech selected</p>
          </div>
        </div>

        <div class="w-full lg:w-[350px] flex flex-col gap-6 flex-shrink-0">
          <div>
            <label class="block font-black mb-2 border-b-2 border-black inline-block text-sm uppercase">
              Thumbnail
              <span v-if="!isEditMode" class="text-red-500">*</span>
              <span v-else class="text-gray-400 text-xs normal-case ml-2">(Biarkan kosong jika tidak diganti)</span>
            </label>
            <div
              class="relative w-full aspect-[4/3] border-4 border-black bg-gray-100 flex flex-col items-center justify-center cursor-pointer hover:bg-white transition-colors group shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
              @click="$refs.fileInput.click()">
              <input
                id="file-upload"
                ref="fileInput"
                type="file"
                class="hidden"
                accept="image/*"
                @change="handleFileChange" />

              <div v-if="previewImage" class="relative w-full h-full p-2 bg-white">
                <img :src="previewImage" class="w-full h-full object-cover border-2 border-black" />
                <button
                  @click.stop="removeImage"
                  class="absolute top-0 right-0 bg-red-500 text-white p-2 border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:scale-110 transition-transform z-10"
                  title="Remove Image">
                  <Icon icon="lucide:trash-2" />
                </button>
              </div>

              <div v-else class="text-center p-6 space-y-3">
                <div
                  class="bg-white p-4 inline-block border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] group-hover:scale-110 transition-transform duration-300">
                  <Icon icon="lucide:image-plus" class="text-4xl text-black" />
                </div>
                <div>
                  <h4 class="font-black text-lg uppercase">{{ isEditMode ? "Change Cover" : "Upload Cover" }}</h4>
                  <p class="text-xs font-mono text-gray-500">Max 2MB (JPG/PNG)</p>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-auto pt-6 border-t-4 border-black border-dashed flex flex-col gap-3">
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full py-4 bg-green-400 border-2 border-black font-black text-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:bg-green-300 hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[4px] active:translate-y-[4px] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3 uppercase italic">
              <template v-if="isLoading">
                <Icon icon="svg-spinners:3-dots-fade" class="text-2xl" />
                <span>{{ isEditMode ? "Updating..." : "Uploading..." }}</span>
              </template>
              <template v-else>
                <span>{{ isEditMode ? "Update Project" : "Launch Project" }}</span>
                <Icon icon="lucide:rocket" />
              </template>
            </button>

            <router-link
              to="/admin/dashboard/projects"
              class="w-full py-3 bg-white text-black border-2 border-black font-bold text-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:bg-gray-100 hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[4px] active:translate-y-[4px] transition-all flex items-center justify-center gap-2 uppercase">
              Cancel
            </router-link>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
