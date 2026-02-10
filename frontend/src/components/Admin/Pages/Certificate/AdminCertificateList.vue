<script setup>
import { ref, onMounted } from "vue";
import { useLocalStorage } from "@vueuse/core";
import { Icon } from "@iconify/vue";
import { getAllCertificates, adminDeleteCertificate } from "../../../lib/api/CertificateApi";
import { alertSuccess, alertError, alertConfirm } from "../../../lib/alert";
// Pastikan alertConfirm ada di alert.js, atau gunakan confirm bawaan browser sementara

const certificates = ref([]);
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
    const response = await getAllCertificates();
    const result = await response.json();
    certificates.value = result.data || result;
  } catch (error) {
    console.error(error);
    alertError("Gagal mengambil data certificates.");
  } finally {
    isLoading.value = false;
  }
};

const handleDelete = async (id) => {
  // Gunakan alert confirm buatanmu atau window.confirm
  // const isConfirmed = alertConfirm("Yakin ingin menghapus sertifikat ini? Data tidak bisa dikembalikan.");
  if (!(await alertConfirm("Yakin ingin menghapus sertifikat ini? Data tidak bisa dikembalikan."))) {
    return;
  }

  try {
    const response = await adminDeleteCertificate(token.value, id);
    if (response.ok) {
      await alertSuccess("Sertifikat berhasil dihapus!");
      fetchData(); // Refresh list
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
        <h1 class="text-3xl md:text-4xl font-black italic uppercase">MANAGE CERTIFICATES</h1>
        <p class="font-mono text-gray-600 mt-2">Showcase your achievements and credentials.</p>
      </div>

      <router-link
        v-if="!isLoading && certificates.length > 0"
        to="/admin/dashboard/certificates/create"
        class="bg-green-400 text-black border-2 border-black px-4 py-2 font-bold uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-y-[4px] active:shadow-none transition-all flex items-center gap-2">
        <Icon icon="lucide:plus" class="text-xl" />
        <span>Add New</span>
      </router-link>
      <!-- <router-link
        to="/admin/dashboard/certificates/create"
        class="bg-yellow-400 text-black border-2 border-black px-4 py-2 font-bold uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-y-[4px] active:shadow-none transition-all flex items-center gap-2">
        <Icon icon="lucide:plus" class="text-xl" />
        <span>Add Certificate</span>
      </router-link> -->
    </div>

    <div v-if="isLoading" class="p-8 text-center font-mono animate-pulse border-4 border-black bg-white">
      LOADING CERTIFICATES...
    </div>

    <div
      v-else-if="certificates.length === 0"
      class="p-12 text-center border-4 border-black bg-white shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] flex flex-col items-center gap-4">
      <div class="bg-gray-100 p-4 rounded-full border-2 border-black">
        <Icon icon="lucide:award" class="text-4xl text-gray-400" />
      </div>
      <div>
        <h3 class="font-bold text-xl uppercase">No Certificates Found</h3>
        <p class="font-mono text-gray-500 mb-6">You haven't uploaded any credentials yet.</p>
        <router-link
          to="/admin/dashboard/certificates/create"
          class="inline-block bg-green-400 text-black border-2 border-black px-6 py-3 font-bold uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:scale-105 transition-transform">
          Add Your First One!
        </router-link>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="cert in certificates"
        :key="cert.id"
        class="border-4 border-black bg-white p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-1 transition-all flex flex-col h-full">
        <div class="relative h-48 w-full border-2 border-black mb-4 bg-gray-100 overflow-hidden group">
          <img :src="getFullUrl(cert.image_path)" class="w-full h-full object-cover" alt="Certificate Image" />

          <a
            v-if="cert.credential_link"
            :href="cert.credential_link"
            target="_blank"
            class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
            <div
              class="bg-white text-black px-3 py-1 font-bold border-2 border-black text-xs uppercase flex items-center gap-1 shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
              View Credential
              <Icon icon="lucide:external-link" />
            </div>
          </a>
        </div>

        <div class="flex-1">
          <div class="flex justify-between items-start mb-2">
            <span class="bg-black text-white text-[10px] px-2 py-0.5 font-mono uppercase font-bold">
              {{ cert.issuer || "Unknown Issuer" }}
            </span>
            <span class="text-xs font-mono text-gray-500">#{{ cert.id }}</span>
          </div>

          <h3 class="font-black text-lg uppercase leading-tight mb-2 line-clamp-2">
            {{ cert.title }}
          </h3>

          <p class="text-sm font-mono text-gray-600 line-clamp-3 mb-4">
            {{ cert.description }}
          </p>
        </div>

        <div class="flex gap-3 mt-auto pt-4 border-t-2 border-black border-dashed">
          <router-link
            :to="`/admin/dashboard/certificates/edit/${cert.id}`"
            class="flex-1 bg-yellow-300 hover:bg-yellow-500 border-2 border-black py-2 font-bold text-center text-sm shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-blue-200 active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all flex items-center justify-center gap-2">
            <Icon icon="lucide:pencil" class="w-4 h-4" />
            Edit
          </router-link>

          <button
            @click="handleDelete(cert.id)"
            class="flex-1 bg-red-400 border-2 border-black py-2 font-bold text-center text-white text-sm shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-red-500 active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all flex items-center justify-center gap-2">
            <Icon icon="lucide:trash-2" class="w-4 h-4" />
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
