<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { getAllProjects } from "../../lib/api/ProjectApi";
import { getAllCertificates } from "../../lib/api/CertificateApi";
import { getSkills } from "../../lib/api/SkillApi";
import { getAllContacts } from "../../lib/api/ContactApi";
import { Icon } from "@iconify/vue";

const stats = ref({
  projects: 0,
  certificates: 0,
  skills: 0,
  contacts: 0,
});

const isLoading = ref(true);
const isDbConnected = ref(false);
const currentTime = ref("");
let timeInterval = null;
let statusInterval = null; // Variable untuk interval cek koneksi

// --- Logic Jam Digital ---
const updateTime = () => {
  currentTime.value = new Date().toLocaleTimeString("en-US", {
    hour12: false,
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
  });
};

// --- Logic Fetch Data (Initial Load) ---
async function fetchData(apiCall, key) {
  try {
    const response = await apiCall();
    if (response.status === 200) {
      isDbConnected.value = true;
      const responseBody = await response.json();
      const data = responseBody.data || responseBody;
      stats.value[key] = Array.isArray(data) ? data.length : 0;
    } else {
      isDbConnected.value = false;
    }
  } catch (e) {
    isDbConnected.value = false;
    console.error(`Error fetching ${key}:`, e);
  }
}

// --- Logic Cek Koneksi (Background Process) ---
// Kita hanya panggil satu API ringan (misal Projects) untuk cek 'denyut nadi' server
async function checkConnection() {
  try {
    // Kita coba panggil API Project saja sebagai sampel
    const response = await getAllProjects();

    if (response.status === 200) {
      // Jika berhasil, update status jadi Connected
      if (!isDbConnected.value) isDbConnected.value = true;
    } else {
      // Jika response code bukan 200 (misal 500 error DB)
      isDbConnected.value = false;
    }
  } catch (e) {
    // Jika fetch error (network down / server mati)
    isDbConnected.value = false;
  }
}

onMounted(async () => {
  // 1. Jalankan Jam
  updateTime();
  timeInterval = setInterval(updateTime, 1000);

  // 2. Load Data Awal (Berat)
  isLoading.value = true;
  await Promise.all([
    fetchData(getAllProjects, "projects"),
    fetchData(getAllCertificates, "certificates"),
    fetchData(getSkills, "skills"),
    fetchData(getAllContacts, "contacts"),
  ]);
  isLoading.value = false;

  // 3. Jalankan Auto-Check Koneksi setiap 5 detik (Ringan)
  statusInterval = setInterval(checkConnection, 5000);
});

onUnmounted(() => {
  // Bersihkan semua interval agar browser tidak berat saat pindah halaman
  if (timeInterval) clearInterval(timeInterval);
  if (statusInterval) clearInterval(statusInterval);
});
</script>

<template>
  <div class="space-y-8 font-sans text-black">
    <div
      class="bg-white border-4 border-black p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] flex justify-between items-center">
      <div>
        <h1 class="text-3xl md:text-4xl font-black uppercase tracking-tighter">
          Dashboard
          <span class="text-blue-600">Overview</span>
        </h1>
        <p class="font-bold font-mono text-gray-600 mt-2">Welcome back, Admin! Here is your portfolio report.</p>
      </div>
      <div
        class="hidden md:block bg-yellow-300 border-2 border-black p-3 rounded-full shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <Icon icon="lucide:layout-dashboard" class="text-4xl" />
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div
        class="bg-blue-200 border-4 border-black p-5 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[-4px] hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] transition-all cursor-default">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-black text-sm uppercase mb-1 tracking-wide">Total Projects</p>
            <h3 class="text-5xl font-black">{{ isLoading ? "..." : stats.projects }}</h3>
          </div>
          <Icon icon="lucide:folder-kanban" class="text-4xl opacity-80" />
        </div>
        <div
          class="mt-4 pt-4 border-t-2 border-black border-dashed text-xs font-bold font-mono flex items-center gap-2">
          <Icon icon="lucide:history" />
          Updated recently
        </div>
      </div>

      <div
        class="bg-green-200 border-4 border-black p-5 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[-4px] hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] transition-all cursor-default">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-black text-sm uppercase mb-1 tracking-wide">Certificates</p>
            <h3 class="text-5xl font-black">{{ isLoading ? "..." : stats.certificates }}</h3>
          </div>
          <Icon icon="lucide:award" class="text-4xl opacity-80" />
        </div>
        <div
          class="mt-4 pt-4 border-t-2 border-black border-dashed text-xs font-bold font-mono flex items-center gap-2">
          <Icon icon="lucide:check-circle" />
          Valid credentials
        </div>
      </div>

      <div
        class="bg-yellow-200 border-4 border-black p-5 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[-4px] hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] transition-all cursor-default">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-black text-sm uppercase mb-1 tracking-wide">Tech Stack</p>
            <h3 class="text-5xl font-black">{{ isLoading ? "..." : stats.skills }}</h3>
          </div>
          <Icon icon="lucide:zap" class="text-4xl opacity-80" />
        </div>
        <div
          class="mt-4 pt-4 border-t-2 border-black border-dashed text-xs font-bold font-mono flex items-center gap-2">
          <Icon icon="lucide:code" />
          Active skills
        </div>
      </div>

      <div
        class="bg-pink-200 border-4 border-black p-5 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[-4px] hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] transition-all cursor-default">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-black text-sm uppercase mb-1 tracking-wide">Social Links</p>
            <h3 class="text-5xl font-black">{{ isLoading ? "..." : stats.contacts }}</h3>
          </div>
          <Icon icon="lucide:share-2" class="text-4xl opacity-80" />
        </div>
        <div
          class="mt-4 pt-4 border-t-2 border-black border-dashed text-xs font-bold font-mono flex items-center gap-2">
          <Icon icon="lucide:globe" />
          Active platforms
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
        <h3 class="font-black text-xl mb-4 border-b-4 border-black pb-2 flex items-center gap-2">
          <Icon icon="lucide:activity" />
          SYSTEM STATUS
        </h3>
        <ul class="space-y-3 font-mono text-sm font-bold">
          <li class="flex justify-between items-center bg-gray-100 p-2 border-2 border-black rounded">
            <span>Backend Connection</span>
            <span v-if="isDbConnected" class="text-green-600 flex items-center gap-1 transition-all duration-500">
              <Icon icon="lucide:wifi" />
              ONLINE (Laravel)
            </span>
            <span v-else class="text-red-600 flex items-center gap-1 animate-pulse transition-all duration-500">
              <Icon icon="lucide:wifi-off" />
              OFFLINE
            </span>
          </li>

          <li class="flex justify-between items-center bg-gray-100 p-2 border-2 border-black rounded">
            <span>Database Status</span>
            <span v-if="isDbConnected" class="text-blue-600 flex items-center gap-1 transition-all duration-500">
              <Icon icon="lucide:database" />
              CONNECTED
            </span>
            <span v-else class="text-red-600 flex items-center gap-1 animate-pulse transition-all duration-500">
              <Icon icon="lucide:database-zap" />
              DISCONNECTED
            </span>
          </li>

          <li class="flex justify-between items-center bg-gray-100 p-2 border-2 border-black rounded">
            <span>Server Time</span>
            <span class="text-gray-600 font-bold font-mono">{{ currentTime }}</span>
          </li>
        </ul>
      </div>

      <div class="bg-white border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
        <h3 class="font-black text-xl mb-4 border-b-4 border-black pb-2 flex items-center gap-2">
          <Icon icon="lucide:zap" />
          QUICK SHORTCUTS
        </h3>
        <div class="grid grid-cols-2 gap-4">
          <router-link
            to="/admin/dashboard/projects"
            class="group bg-blue-100 border-2 border-black p-3 hover:bg-blue-300 hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all text-center flex flex-col items-center justify-center gap-1 cursor-pointer">
            <Icon icon="lucide:folder-plus" class="text-2xl group-hover:scale-110 transition-transform" />
            <span class="font-bold text-xs uppercase">Manage Projects</span>
          </router-link>

          <router-link
            to="/admin/dashboard/certificates"
            class="group bg-green-100 border-2 border-black p-3 hover:bg-green-300 hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all text-center flex flex-col items-center justify-center gap-1 cursor-pointer">
            <Icon icon="lucide:award" class="text-2xl group-hover:scale-110 transition-transform" />
            <span class="font-bold text-xs uppercase">Manage Certificates</span>
          </router-link>

          <router-link
            to="/admin/dashboard/skills"
            class="group bg-yellow-100 border-2 border-black p-3 hover:bg-yellow-300 hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all text-center flex flex-col items-center justify-center gap-1 cursor-pointer">
            <Icon icon="lucide:cpu" class="text-2xl group-hover:scale-110 transition-transform" />
            <span class="font-bold text-xs uppercase">Manage Skills</span>
          </router-link>

          <router-link
            to="/admin/dashboard/contacts"
            class="group bg-pink-100 border-2 border-black p-3 hover:bg-pink-300 hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all text-center flex flex-col items-center justify-center gap-1 cursor-pointer">
            <Icon icon="lucide:share-2" class="text-2xl group-hover:scale-110 transition-transform" />
            <span class="font-bold text-xs uppercase">Manage Links</span>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>
