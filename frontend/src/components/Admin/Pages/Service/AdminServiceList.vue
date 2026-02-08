<script setup>
import { ref, onMounted, reactive, nextTick } from "vue";
import { useLocalStorage } from "@vueuse/core";
import { Icon } from "@iconify/vue";
// Import API sesuai file ServiceApi.js milikmu
import {
  getAllServices,
  adminUploadService,
  adminUpdateService,
  adminDeleteService,
} from "../../../lib/api/ServiceApi";
import { alertSuccess, alertError, alertConfirm } from "../../../lib/alert";

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

// Opsi icon untuk mempercepat input (Tema Comic)
const commonIcons = [
  { name: "Web Dev", id: "lucide:code-2" },
  { name: "Design", id: "lucide:palette" },
  { name: "Mobile", id: "lucide:smartphone" },
  { name: "Server", id: "lucide:server" },
  { name: "Maintenance", id: "lucide:settings" },
];

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
    // Menggunakan payload JSON biasa karena tidak ada upload gambar
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
      :class="[
        'border-4 border-black p-4 md:p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] mb-12 transition-all scroll-mt-24', // p-4 di mobile agar tidak mepet
        isEditing ? 'bg-yellow-50' : 'bg-white',
      ]">
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
                class="w-full p-3 md:p-4 border-2 border-black font-mono focus:bg-blue-100 outline-none text-sm" />
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
          <label class="block font-bold mb-2 uppercase border-b-2 border-black inline-block text-sm md:text-base">
            Description
          </label>
          <textarea
            v-model="form.description"
            rows="3"
            class="w-full p-3 md:p-4 border-2 border-black font-mono outline-none text-sm"></textarea>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center gap-6 border-t-2 border-black pt-6">
          <label class="flex items-center gap-3 cursor-pointer group self-start md:self-center">
            <input type="checkbox" v-model="form.is_active" class="hidden peer" />
            <div
              class="w-12 h-6 border-2 border-black bg-gray-200 peer-checked:bg-green-400 relative transition-colors shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex-shrink-0">
              <div
                class="absolute top-1/2 -translate-y-1/2 left-1 w-3 h-3 bg-black transition-all peer-checked:left-7"></div>
            </div>
            <span class="font-black text-sm uppercase">Active Status</span>
          </label>

          <div class="flex gap-3 w-full md:w-auto">
            <button
              v-if="isEditing"
              @click="cancelEdit"
              type="button"
              class="flex-1 md:flex-none px-4 md:px-6 py-3 font-bold border-2 border-black hover:bg-red-100 transition-colors text-sm">
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
        :class="[
          'border-4 p-4 md:p-6 transition-all flex flex-col relative group', // p-4 di mobile
          service.is_active
            ? 'bg-white border-black hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)]'
            : 'bg-gray-100 border-gray-400 grayscale-[0.5] opacity-80 shadow-none',
        ]">
        <div
          v-if="!service.is_active"
          class="absolute top-2 right-2 bg-gray-500 text-white text-[10px] px-2 py-1 font-bold border-2 border-black rotate-12 z-10">
          INACTIVE
        </div>

        <div
          :class="[
            'mb-4 w-12 h-12 md:w-14 md:h-14 border-2 flex items-center justify-center flex-shrink-0 aspect-square shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]',
            service.is_active ? 'bg-yellow-300 border-black' : 'bg-gray-300 border-gray-400 shadow-none',
          ]">
          <Icon :icon="service.icon" class="text-2xl md:text-3xl" />
        </div>

        <h3 :class="['font-black text-lg md:text-xl mb-2 uppercase', !service.is_active && 'text-gray-500']">
          {{ service.title }}
        </h3>
        <p
          :class="[
            'font-mono text-xs md:text-sm mb-4 flex-1 line-clamp-3',
            service.is_active ? 'text-gray-600' : 'text-gray-400',
          ]">
          {{ service.description }}
        </p>

        <div
          :class="[
            'border-t-2 pt-4 mt-auto flex justify-between items-center',
            service.is_active ? 'border-black' : 'border-gray-300',
          ]">
          <span :class="['font-black text-[10px] md:text-xs font-mono', !service.is_active && 'text-gray-400']">
            {{ service.price || "Contact for price" }}
          </span>
          <div class="flex gap-2">
            <button
              @click="startEdit(service)"
              class="p-2 border-2 border-black bg-blue-400 hover:bg-blue-500 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none">
              <Icon icon="lucide:edit-3" width="18" />
            </button>
            <button
              @click="handleDelete(service.id)"
              class="p-2 border-2 border-black bg-red-500 text-white hover:bg-red-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none">
              <Icon icon="lucide:trash-2" width="18" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
