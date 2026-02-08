<script setup>
import { ref, onMounted, reactive, computed, nextTick } from "vue"; // Tambah nextTick
import { useLocalStorage } from "@vueuse/core";
import { Icon } from "@iconify/vue";
import { getSkills, addSkill, deleteSkill, updateSkill } from "../../llib/api/SkillApi";
import { alertSuccess, alertError, alertConfirm } from "../../llib/alert";
import Swal from "sweetalert2";

const token = useLocalStorage("token", "");
const skills = ref([]);
const isLoading = ref(false);
const isSubmitting = ref(false);

const isEditing = ref(false);
const editId = ref(null);

// 1. BUAT REF UNTUK FORM (Agar bisa di-scroll)
const formTopRef = ref(null);

// ... (Bagian commonTechStacks, form, filteredSuggestions, selectTech SAMA SEPERTI SEBELUMNYA) ...
// Agar kode tidak kepanjangan, bagian list commonTechStacks saya skip di sini (pakai yang lama)
const commonTechStacks = [
  { name: "Laravel", id: "simple-icons:laravel" },
  { name: "Vue.js", id: "simple-icons:vuedotjs" },
  { name: "React", id: "simple-icons:react" },
  { name: "Node.js", id: "simple-icons:nodedotjs" },
  { name: "Tailwind CSS", id: "simple-icons:tailwindcss" },
  { name: "Bootstrap", id: "simple-icons:bootstrap" },
  { name: "PHP", id: "simple-icons:php" },
  { name: "JavaScript", id: "simple-icons:javascript" },
  { name: "TypeScript", id: "simple-icons:typescript" },
  { name: "Python", id: "simple-icons:python" },
  { name: "MySQL", id: "simple-icons:mysql" },
  { name: "PostgreSQL", id: "simple-icons:postgresql" },
  { name: "MongoDB", id: "simple-icons:mongodb" },
  { name: "Git", id: "simple-icons:git" },
  { name: "GitHub", id: "simple-icons:github" },
  { name: "Docker", id: "simple-icons:docker" },
  { name: "Figma", id: "simple-icons:figma" },
  { name: "HTML5", id: "simple-icons:html5" },
  { name: "CSS3", id: "simple-icons:css3" },
  { name: "Sass", id: "simple-icons:sass" },
  { name: "Postman", id: "simple-icons:postman" },
  { name: "Linux", id: "simple-icons:linux" },
  { name: "Nginx", id: "simple-icons:nginx" },
  { name: "Next.js", id: "simple-icons:nextdotjs" },
  { name: "Apache", id: "simple-icons:apache" },
  { name: "npm", id: "simple-icons:npm" },
  { name: "pnpm", id: "simple-icons:pnpm" }, // Opsional: Kalau kamu pakai pnpm (lagi hits karena cepat)
  { name: "Yarn", id: "simple-icons:yarn" },
  { name: "Express", id: "simple-icons:express" }, // Opsional: Teman setianya Node.js
  { name: "Redux", id: "simple-icons:redux" }, // Opsional: State Management
  { name: "Prisma", id: "simple-icons:prisma" },
];

const form = reactive({ name: "", identifier: "" });
const showSuggestions = ref(false);

const filteredSuggestions = computed(() => {
  if (!form.name) return [];
  return commonTechStacks.filter((tech) => tech.name.toLowerCase().includes(form.name.toLowerCase()));
});

const selectTech = (tech) => {
  form.name = tech.name;
  form.identifier = tech.id;
  showSuggestions.value = false;
};

// Fetch Data
const fetchData = async () => {
  isLoading.value = true;
  try {
    const response = await getSkills();
    const responseBody = await response.json();
    skills.value = responseBody.data || responseBody;
  } catch (error) {
    console.error(error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchData);

// --- LOGIC EDIT DENGAN SCROLL FIX ---
const startEdit = (skill) => {
  isEditing.value = true;
  editId.value = skill.id;
  form.name = skill.name;
  form.identifier = skill.identifier;

  // FIX: Scroll ke elemen form, bukan window
  // nextTick memastikan DOM sudah siap sebelum discroll
  nextTick(() => {
    if (formTopRef.value) {
      formTopRef.value.scrollIntoView({
        behavior: "smooth",
        block: "center", // Scroll sampai elemen ada di tengah/atas layar
      });
    }
  });
};

const cancelEdit = () => {
  isEditing.value = false;
  editId.value = null;
  form.name = "";
  form.identifier = "";
};

const handleSubmit = async () => {
  if (!form.name || !form.identifier) {
    alertError("Nama Skill dan Icon tidak boleh kosong!");
    return;
  }
  isSubmitting.value = true;
  try {
    let response;
    if (isEditing.value) {
      const payload = { name: form.name, identifier: form.identifier };
      response = await updateSkill(token.value, editId.value, payload);
    } else {
      const formData = new FormData();
      formData.append("name", form.name);
      formData.append("identifier", form.identifier);
      response = await addSkill(token.value, formData);
    }

    if (response.ok) {
      await alertSuccess(isEditing.value ? "Skill berhasil diupdate!" : "Skill berhasil ditambahkan!");
      cancelEdit();
      fetchData();
    } else {
      const err = await response.json();
      alertError(err.message || "Gagal menyimpan skill");
    }
  } catch (e) {
    console.error(e);
    alertError("Terjadi kesalahan sistem");
  } finally {
    isSubmitting.value = false;
  }
};

const handleDelete = async (id) => {
  if (!(await alertConfirm("Yakin ingin menghapus skill ini?"))) {
    return;
  }

  try {
    const response = await deleteSkill(token.value, id);
    const responseBody = await response.json();
    console.log(responseBody);

    if (response.status === 200) {
      await alertSuccess("Skill dihapus!");
      await fetchData();
    } else {
      await alertError(responseBody.message);
    }
  } catch (e) {
    alertError("Gagal menghapus");
  }
};
</script>

<template>
  <div class="p-6 max-w-7xl mx-auto">
    <div class="mb-10 border-b-4 border-black pb-4 flex justify-between items-end">
      <div>
        <h1 class="text-3xl md:text-4xl font-black italic">SKILL MANAGER</h1>
        <p class="font-mono text-gray-600 mt-2 text-sm md:text-base">Manage your tech stack efficiently.</p>
      </div>
      <div class="hidden md:block bg-black text-white px-3 py-1 font-mono font-bold">{{ skills.length }} ITEMS</div>
    </div>

    <div
      ref="formTopRef"
      :class="[
        'border-4 border-black p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] mb-12 transition-colors scroll-mt-24', // scroll-mt-24 memberi jarak napas saat discroll
        isEditing ? 'bg-yellow-50' : 'bg-white',
      ]">
      <h2 class="font-black text-xl md:text-2xl mb-6 flex items-center gap-2">
        <Icon :icon="isEditing ? 'lucide:edit' : 'lucide:plus-circle'" />
        {{ isEditing ? "EDIT SKILL" : "ADD NEW SKILL" }}
      </h2>

      <form @submit.prevent="handleSubmit" class="flex flex-col md:flex-row gap-6 items-start">
        <div class="w-full md:flex-1 relative">
          <label class="block font-bold mb-2 border-b-2 border-black inline-block">TECH NAME</label>
          <input
            v-model="form.name"
            @focus="showSuggestions = true"
            @input="showSuggestions = true"
            type="text"
            placeholder="Ketik nama (misal: React)"
            class="w-full p-4 border-2 border-black font-mono focus:bg-yellow-100 focus:outline-none transition-colors"
            autocomplete="off" />
          <div
            v-if="showSuggestions && filteredSuggestions.length > 0"
            class="absolute z-10 w-full bg-white border-2 border-t-0 border-black shadow-lg max-h-48 overflow-y-auto">
            <div
              v-for="tech in filteredSuggestions"
              :key="tech.id"
              @click="selectTech(tech)"
              class="p-3 hover:bg-black hover:text-white cursor-pointer flex items-center gap-3 border-b border-gray-200">
              <Icon :icon="tech.id" class="text-xl" />
              <span class="font-bold">{{ tech.name }}</span>
            </div>
          </div>
        </div>

        <div class="w-full md:flex-1">
          <label class="block font-bold mb-2 border-b-2 border-black inline-block">ICON CODE (AUTO)</label>
          <div class="flex items-center gap-2">
            <input
              v-model="form.identifier"
              type="text"
              placeholder="simple-icons:..."
              class="w-full p-4 border-2 border-black font-mono bg-gray-100 focus:bg-white focus:outline-none" />
            <div
              class="w-14 h-14 border-2 border-black flex items-center justify-center bg-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex-shrink-0">
              <Icon :icon="form.identifier" v-if="form.identifier" class="text-3xl" />
              <span v-else class="text-xs text-gray-400">N/A</span>
            </div>
          </div>
          <p class="text-xs mt-1 text-gray-500 font-mono">
            *Code otomatis terisi jika memilih dari saran. Atau cari manual di
            <a href="https://icones.js.org/" target="_blank" class="underline font-bold text-blue-600">Icones</a>
            .
          </p>
        </div>

        <div class="flex flex-col gap-2 mt-2 md:mt-8 w-full md:w-auto">
          <button
            type="submit"
            :disabled="isSubmitting"
            :class="[
              'h-[58px] font-black px-8 border-2 border-transparent shadow-[4px_4px_0px_0px_rgba(0,0,0,0)] hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all disabled:opacity-50',
              isEditing
                ? 'bg-yellow-400 text-black border-black hover:bg-yellow-500'
                : 'bg-black text-white hover:bg-white hover:text-black hover:border-black',
            ]">
            {{ isSubmitting ? "SAVING..." : isEditing ? "UPDATE SKILL" : "SAVE SKILL" }}
          </button>
          <button
            v-if="isEditing"
            @click="cancelEdit"
            type="button"
            class="text-xs font-bold text-red-600 underline hover:text-red-800 text-center">
            BATAL EDIT
          </button>
        </div>
      </form>
    </div>

    <div v-if="isLoading" class="text-center font-mono py-10">Loading Data...</div>

    <div v-else class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
      <div
        v-for="skill in skills"
        :key="skill.id"
        class="group relative bg-white border-2 border-black flex flex-col items-center hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] transition-all duration-200">
        <div class="p-4 flex flex-col items-center gap-4 w-full">
          <div class="w-16 h-16 flex items-center justify-center">
            <Icon :icon="skill.identifier" :key="skill.identifier" class="text-5xl" />
          </div>
          <div class="text-center w-full pt-2">
            <h3 class="font-black font-mono text-sm truncate">{{ skill.name }}</h3>
          </div>
        </div>

        <div
          class="hidden md:flex absolute inset-0 bg-white/90 opacity-0 group-hover:opacity-100 transition-opacity items-center justify-center gap-2 backdrop-blur-[1px]">
          <button
            @click="startEdit(skill)"
            class="bg-yellow-400 text-black border-2 border-black p-2 hover:scale-110 transition-transform shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]"
            title="Edit">
            <Icon icon="lucide:edit-2" width="20" />
          </button>
          <button
            @click="handleDelete(skill.id)"
            class="bg-red-500 text-white border-2 border-black p-2 hover:scale-110 transition-transform shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]"
            title="Hapus">
            <Icon icon="lucide:trash-2" width="20" />
          </button>
        </div>

        <div class="flex md:hidden w-full border-t-2 border-black">
          <button
            @click="startEdit(skill)"
            class="flex-1 bg-yellow-300 py-3 flex items-center justify-center border-r-2 border-black active:bg-yellow-500">
            <Icon icon="lucide:edit-2" width="16" />
          </button>
          <button
            @click="handleDelete(skill.id)"
            class="flex-1 bg-red-500 text-white py-3 flex items-center justify-center active:bg-red-700">
            <Icon icon="lucide:trash-2" width="16" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
