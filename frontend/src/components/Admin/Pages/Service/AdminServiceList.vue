<script setup>
import { ref, onMounted, reactive, nextTick } from "vue";
import { useLocalStorage } from "@vueuse/core";
import { Icon } from "@iconify/vue";
import {
  getAllServices,
  adminUploadService,
  adminUpdateService,
  adminDeleteService,
} from "../../../../lib/api/ServiceApi";
import { alertSuccess, alertError, alertConfirm } from "../../../../lib/alert";
import { marked } from "marked";

const token = useLocalStorage("token", "");
const services = ref([]);
const isLoading = ref(false);
const isSubmitting = ref(false);

const isEditing = ref(false);
const editId = ref(null);
const formTopRef = ref(null);

const form = reactive({
  title: "",
  description: "",
  icon: "lucide:layout",
  price: "",
  cta_link: "",
  is_active: true,
});

const commonIcons = [
  { name: "Web Dev", id: "lucide:code-2" },
  { name: "Design", id: "lucide:palette" },
  { name: "Mobile", id: "lucide:smartphone" },
  { name: "Server", id: "lucide:server" },
  { name: "Maintenance", id: "lucide:settings" },
];

const renderMarkdown = (text) => {
  if (!text) return "";
  return marked.parse(text, { breaks: true });
};

const fetchData = async () => {
  isLoading.value = true;
  try {
    const response = await getAllServices(true);
    const responseBody = await response.json();
    services.value = responseBody.data || responseBody;
  } catch (error) {
    console.error(error);
    alertError("Gagal mengambil data service");
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchData);

// --- FITUR BARU: TOGGLE STATUS LANGSUNG ---
const toggleStatus = async (service) => {
  const newStatus = !service.is_active;

  // Optimistic UI Update (Ubah tampilan dulu biar cepat)
  const originalStatus = service.is_active;
  service.is_active = newStatus;

  try {
    // Siapkan payload lengkap agar data lain tidak hilang
    const payload = {
      title: service.title,
      description: service.description,
      icon: service.icon,
      price: service.price,
      cta_link: service.cta_link,
      is_active: newStatus,
    };

    const response = await adminUpdateService(token.value, service.id, payload);

    if (response.ok) {
      // Tidak perlu alertSuccess agar tidak mengganggu flow, cukup visual berubah
      console.log("Status updated");
    } else {
      // Revert jika gagal
      service.is_active = originalStatus;
      const err = await response.json();
      alertError(err.message || "Gagal mengubah status");
    }
  } catch (e) {
    service.is_active = originalStatus;
    alertError("Terjadi kesalahan sistem");
  }
};
// ------------------------------------------

const startEdit = (service) => {
  isEditing.value = true;
  editId.value = service.id;
  form.title = service.title;
  form.description = service.description;
  form.icon = service.icon;
  form.price = service.price;
  form.cta_link = service.cta_link;
  form.is_active = !!service.is_active;

  nextTick(() => {
    if (formTopRef.value) {
      formTopRef.value.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  });
};

const cancelEdit = () => {
  isEditing.value = false;
  editId.value = null;
  Object.assign(form, {
    title: "",
    description: "",
    icon: "lucide:layout",
    price: "",
    cta_link: "",
    is_active: true,
  });
};

const handleSubmit = async () => {
  if (!form.title || !form.icon) {
    alertError("Judul dan Icon wajib diisi!");
    return;
  }

  isSubmitting.value = true;
  try {
    let response;
    const payload = { ...form };

    if (isEditing.value) {
      response = await adminUpdateService(token.value, editId.value, payload);
    } else {
      response = await adminUploadService(token.value, payload);
    }

    if (response.ok) {
      await alertSuccess(isEditing.value ? "Service diperbarui!" : "Service ditambahkan!");
      cancelEdit();
      fetchData();
    } else {
      const err = await response.json();
      alertError(err.message || "Gagal menyimpan data");
    }
  } catch (e) {
    alertError("Terjadi kesalahan sistem");
  } finally {
    isSubmitting.value = false;
  }
};

const handleDelete = async (id) => {
  if (!(await alertConfirm("Hapus layanan ini?"))) return;

  try {
    const response = await adminDeleteService(token.value, id);
    if (response.ok) {
      await alertSuccess("Layanan dihapus!");
      fetchData();
    } else {
      alertError("Gagal menghapus");
    }
  } catch (e) {
    alertError("Kesalahan koneksi");
  }
};
</script>

<template>
  <div class="p-4 md:p-6 max-w-7xl mx-auto">
    <div class="mb-8 md:mb-10 border-b-4 border-black pb-4 flex justify-between items-end">
      <div>
        <h1 class="text-2xl md:text-4xl font-black italic uppercase text-black">Service Manager</h1>
        <p class="font-mono text-gray-600 mt-2 text-xs md:text-base">What services are you offering today?</p>
      </div>
      <div class="hidden md:block bg-black text-white px-3 py-1 font-mono font-bold">
        {{ services.length }} SERVICES
      </div>
    </div>

    <div
      ref="formTopRef"
      :class="[ 'border-4 border-black p-4 md:p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] mb-12 transition-all scroll-mt-24', isEditing ? 'bg-gray-50' : 'bg-white', ]">
      <h2 class="font-black text-lg md:text-2xl mb-6 flex items-center gap-2">
        <Icon :icon="isEditing ? 'lucide:pencil-line' : 'lucide:package-plus'" />
        {{ isEditing ? "EDIT SERVICE" : "ADD NEW SERVICE" }}
      </h2>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <div>
              <label class="block font-bold mb-2 uppercase border-b-2 border-black inline-block text-sm md:text-base">
                Service Title
              </label>
              <input
                v-model="form.title"
                type="text"
                placeholder="e.g. Fullstack Development"
                class="w-full p-3 md:p-4 border-2 border-black font-mono focus:bg-gray-100 outline-none text-sm" />
            </div>

            <div>
              <label class="block font-bold mb-2 uppercase border-b-2 border-black inline-block text-sm md:text-base">
                Icon Identifier
              </label>
              <div class="flex gap-2">
                <input
                  v-model="form.icon"
                  type="text"
                  class="flex-1 p-3 md:p-4 border-2 border-black font-mono bg-gray-50 text-sm" />
                <div
                  class="w-12 h-12 md:w-16 md:h-16 border-2 border-black bg-white flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex-shrink-0 aspect-square">
                  <Icon :icon="form.icon" class="text-2xl md:text-3xl" />
                </div>
              </div>
              <div class="flex flex-wrap gap-2 mt-3">
                <button
                  v-for="ic in commonIcons"
                  :key="ic.id"
                  type="button"
                  @click="form.icon = ic.id"
                  class="text-[10px] font-bold border border-black px-2 py-1 hover:bg-black hover:text-white transition-colors">
                  {{ ic.name }}
                </button>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block font-bold mb-2 uppercase border-b-2 border-black inline-block text-sm md:text-base">
                Starting Price (Optional)
              </label>
              <input
                v-model="form.price"
                type="text"
                placeholder="e.g. $500 / Project"
                class="w-full p-3 md:p-4 border-2 border-black font-mono outline-none text-sm" />
            </div>
            <div>
              <label class="block font-bold mb-2 uppercase border-b-2 border-black inline-block text-sm md:text-base">
                CTA / WhatsApp Link
              </label>
              <input
                v-model="form.cta_link"
                type="text"
                placeholder="https://wa.me/..."
                class="w-full p-3 md:p-4 border-2 border-black font-mono outline-none text-sm" />
            </div>
          </div>
        </div>

        <div>
          <label class="block font-bold mb-2 uppercase flex justify-between">
            <span class="border-b-2 border-black inline-block text-sm md:text-base">
              Description (Markdown Supported)
            </span>
            <span class="text-[10px] text-gray-400 capitalize font-mono mt-1">
              use **bold**, *italic*, or - bullets
            </span>
          </label>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <textarea
              v-model="form.description"
              rows="4"
              placeholder="Jelaskan detail layanannya..."
              class="w-full p-3 md:p-4 border-2 border-black font-mono focus:bg-gray-50 outline-none text-sm resize-y"></textarea>

            <div class="border-2 border-black border-dashed p-3 bg-gray-50 overflow-y-auto max-h-[150px]">
              <div class="text-[10px] font-black uppercase text-gray-400 mb-2">Live Preview:</div>
              <div v-html="renderMarkdown(form.description)" class="markdown-preview font-mono text-sm"></div>
            </div>
          </div>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center gap-6 border-t-2 border-black pt-6">
          <label class="flex items-center gap-3 cursor-pointer group self-start md:self-center">
            <input type="checkbox" v-model="form.is_active" class="hidden peer" />
            <!-- Custom Toggle -->
            <div
              class="w-16 h-8 border-[3px] border-black relative shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] flex-shrink-0 flex items-center px-1 overflow-hidden transition-colors duration-300"
              :class="form.is_active ? 'bg-green-400' : 'bg-red-400'">
              
              <!-- Container Text ON/OFF (Bergantian masuk/keluar) -->
              <div class="absolute inset-0 flex items-center justify-between px-1 pointer-events-none">
                <span 
                  class="text-[10px] font-black text-black transition-transform duration-300 w-1/2 text-left ml-0.5"
                  :class="form.is_active ? 'translate-y-0 opacity-100' : '-translate-y-4 opacity-0'">
                  ON
                </span>
                <span 
                  class="text-[10px] font-black text-white transition-transform duration-300 w-1/2 text-right mr-0.5"
                  :class="form.is_active ? 'translate-y-4 opacity-0' : 'translate-y-0 opacity-100'">
                  OFF
                </span>
              </div>

              <!-- Lingkaran Saklar (Bergerak Kiri/Kanan) -->
              <div
                class="w-5 h-5 bg-white border-2 border-black transition-transform duration-300 z-10"
                :class="form.is_active ? 'translate-x-[32px]' : 'translate-x-0'"></div>
            </div>
            <div class="flex flex-col">
              <span class="font-black text-sm uppercase leading-tight">Status</span>
              <span class="text-[10px] font-mono font-bold" :class="form.is_active ? 'text-black' : 'text-gray-500'">
                {{ form.is_active ? 'TERLIHAT OLEH PUBLIK' : 'DISEMBUNYIKAN' }}
              </span>
            </div>
          </label>

          <div class="flex gap-3 w-full md:w-auto">
            <button
              v-if="isEditing"
              @click="cancelEdit"
              type="button"
              class="flex-1 md:flex-none px-4 md:px-6 py-3 font-bold border-2 border-black hover:bg-black hover:text-white transition-colors text-sm">
              CANCEL
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="flex-1 md:flex-none px-6 md:px-10 py-3 font-black bg-black text-white border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all disabled:opacity-50 text-sm">
              {{ isSubmitting ? "PROCESSING..." : isEditing ? "UPDATE SERVICE" : "SAVE SERVICE" }}
            </button>
          </div>
        </div>
      </form>
    </div>

    <div v-if="isLoading" class="text-center font-mono py-10">LOADING SERVICES...</div>

    <div
      v-else-if="services.length === 0"
      class="text-center py-12 border-4 border-black bg-gray-50 flex flex-col items-center gap-4">
      <Icon icon="lucide:ghost" class="text-6xl text-gray-300" />
      <div>
        <h3 class="font-black text-xl uppercase">Nothing here yet</h3>
        <p class="font-mono text-sm text-gray-500">Start adding your first service above!</p>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
      <div
        v-for="service in services"
        :key="service.id"
        :class="[ 'border-4 p-4 md:p-6 transition-all flex flex-col relative group', service.is_active ? 'bg-white border-black hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)]' : 'bg-gray-100 border-gray-400 grayscale-[0.5] opacity-80 shadow-none', ]">
        <div
          v-if="!service.is_active"
          class="absolute top-2 right-2 bg-gray-500 text-white text-[10px] px-2 py-1 font-bold border-2 border-black rotate-12 z-10">
          INACTIVE
        </div>

        <div
          :class="[ 'mb-4 w-12 h-12 md:w-14 md:h-14 border-2 flex items-center justify-center flex-shrink-0 aspect-square shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]', service.is_active ? 'bg-gray-200 border-black' : 'bg-gray-300 border-gray-400 shadow-none', ]">
          <Icon :icon="service.icon" class="text-2xl md:text-3xl" />
        </div>

        <h3 :class="['font-black text-lg md:text-xl mb-2 uppercase', !service.is_active && 'text-gray-500']">
          {{ service.title }}
        </h3>
        <div
          v-html="renderMarkdown(service.description)"
          :class="[ 'markdown-preview font-mono text-xs md:text-sm mb-4 flex-1 line-clamp-3', service.is_active ? 'text-gray-600' : 'text-gray-400', ]"></div>

        <div
          :class="[ 'border-t-2 pt-4 mt-auto flex justify-between items-center', service.is_active ? 'border-black' : 'border-gray-300', ]">
          <span :class="['font-black text-[10px] md:text-xs font-mono', !service.is_active && 'text-gray-400']">
            {{ service.price || "Contact for price" }}
          </span>
          <div class="flex gap-2">
            <button
              @click="toggleStatus(service)"
              :title="service.is_active ? 'Nonaktifkan' : 'Aktifkan'"
              :class="[ 'p-2 border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none transition-colors', service.is_active ? 'bg-black text-white hover:text-black hover:bg-gray-100 hover:bg-black text-white text-black' : 'bg-gray-300 hover:bg-gray-400 text-gray-700', ]">
              <Icon :icon="service.is_active ? 'lucide:power' : 'lucide:power-off'" width="18" />
            </button>
            <button
              @click="startEdit(service)"
              class="p-2 border-2 border-black bg-gray-200 hover:bg-gray-500 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none">
              <Icon icon="lucide:edit-3" width="18" />
            </button>
            <button
              @click="handleDelete(service.id)"
              class="bg-red-500 text-white hover:bg-red-600 p-2 border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none">
              <Icon icon="lucide:trash-2" width="18" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
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
}
.markdown-preview :deep(p) {
  margin-bottom: 0.5rem;
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
