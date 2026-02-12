<script setup>
import { ref, onMounted } from "vue";
import { getProfile } from "../lib/api/ProfileApi";
// Import API lain jika ada, misal: import { getProjects } from "../../lib/api/ProjectApi";

import LoadingScreen from "./LoadingScreen.vue"; // Import Loader yang baru dibuat
import Experience from "./Section/Experience.vue";
import FeaturedCertificate from "./Section/FeaturedCertificate.vue";
import FeaturedProject from "./Section/FeaturedProject.vue";
import Footer from "./Section/Footer.vue";
import HaveAnIdea from "./Section/HaveAnIdea.vue";
import Hero from "./Section/Hero.vue";
import Navbar from "./Section/Navbar.vue";
import Tech from "./Section/Tech.vue";
import { getAllProjects } from "../lib/api/ProjectApi";
import { getSkills } from "../lib/api/SkillApi";
import { getAllCertificates } from "../lib/api/CertificateApi";
import { getAllExperiences } from "../lib/api/ExperienceApi";

// State Management
const isLoading = ref(true);
const profileData = ref(null);
const skillData = ref([]);
const projectData = ref([]); // Contoh untuk data lain
const certificateData = ref([]);
const experienceData = ref([]);

// Tambahkan Fungsi Helper untuk Preload Gambar
function preloadImage(url) {
  return new Promise((resolve, reject) => {
    if (!url) {
      resolve(); // Skip jika url kosong
      return;
    }
    const img = new Image();
    img.src = url;
    img.onload = resolve;
    img.onerror = resolve; // Tetap resolve meski error biar ga stuck loading selamanya
  });
}

// Fungsi Fetch Data Utama
async function fetchAllData() {
  try {
    // Gunakan Promise.all agar request berjalan bersamaan (Parallel Fetching)
    // Tambahkan request lain ke dalam array ini jika section lain butuh data API
    const [profileRes, skillRes, projectRes, certificateRes, experienceRes] = await Promise.all([
      getProfile(),
      getSkills(),
      getAllProjects(),
      getAllCertificates(),
      getAllExperiences(),
    ]);

    // Parsing JSON
    const profileJson = await profileRes.json();
    const skillJson = await skillRes.json();
    const projectJson = await projectRes.json();
    const certificateJson = await certificateRes.json();
    const experienceJson = await experienceRes.json();
    console.log(experienceJson);

    // Assign ke state (Pastikan validasi status 200)
    if (profileRes.ok) {
      profileData.value = profileJson.data || profileJson;
    }

    if (profileData.value && profileData.value.about && profileData.value.about.photo_url) {
      await preloadImage(profileData.value.about.photo_url);
    }

    if (skillRes.ok) {
      skillData.value = skillJson.data || skillJson;
    }

    if (projectRes.ok) {
      projectData.value = projectJson.data || projectJson;
    }
    if (certificateRes.ok) {
      certificateData.value = certificateJson.data || certificateJson;
    }
    if (experienceRes.ok) {
      experienceData.value = experienceJson.data || experienceJson;
    }
  } catch (e) {
    console.error("Gagal load data", e);
  } finally {
    setTimeout(() => {
      isLoading.value = false;
    }, 1000);
  }
}

onMounted(() => {
  fetchAllData();
});
</script>

<template>
  <div
    class="min-h-screen bg-white text-black font-sans selection:bg-black selection:text-white overflow-x-hidden flex flex-col pb-24 md:pb-0">
    <Transition name="fade">
      <LoadingScreen v-if="isLoading" />
    </Transition>

    <div v-if="!isLoading" class="animate-in">
      <Navbar />

      <Hero :profile="profileData" />

      <Tech :skills="skillData" />

      <FeaturedProject :projects="projectData" />
      <FeaturedCertificate :certificates="certificateData" />
      <Experience :experiences="experienceData" />

      <HaveAnIdea />
      <Footer />
    </div>
  </div>
</template>

<style scoped>
/* Transisi Fade Out untuk Loading Screen */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Animasi sederhana saat konten muncul */
.animate-in {
  animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
