<script setup>
import { ref, onMounted, watch, nextTick, onUnmounted } from "vue";
// --- 1. Import GSAP dan ScrollTrigger ---
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

import { getProfile } from "../lib/api/ProfileApi";
import { getAllProjects } from "../lib/api/ProjectApi";
import { getSkills } from "../lib/api/SkillApi";
import { getAllCertificates } from "../lib/api/CertificateApi";
import { getAllExperiences } from "../lib/api/ExperienceApi";

import LoadingScreen from "./LoadingScreen.vue";
import Experience from "./Section/Experience.vue";
import FeaturedCertificate from "./Section/FeaturedCertificate.vue";
import FeaturedProject from "./Section/FeaturedProject.vue";
import Footer from "./Section/Footer.vue";
import HaveAnIdea from "./Section/HaveAnIdea.vue";
import Hero from "./Section/Hero.vue";
import Navbar from "./Section/Navbar.vue";
import Tech from "./Section/Tech.vue";

// Register GSAP Plugin
gsap.registerPlugin(ScrollTrigger);

// State Management
const isLoading = ref(true);
const isError = ref(false);
const errorMessage = ref("");

const profileData = ref(null);
const skillData = ref([]);
const projectData = ref([]);
const certificateData = ref([]);
const experienceData = ref([]);

// --- 2. Refs untuk Element Section ---
const projectSectionRef = ref(null);
const certificateSectionRef = ref(null);
let scrollTriggerInstance = null; // Untuk menyimpan instance agar bisa dibersihkan

// Fungsi Helper untuk Preload Gambar
function preloadImage(url) {
  return new Promise((resolve) => {
    if (!url) {
      resolve();
      return;
    }
    const img = new Image();
    img.src = url;
    img.onload = resolve;
    img.onerror = resolve;
  });
}

// Fungsi Fetch Data Utama
async function fetchAllData() {
  isLoading.value = true;
  isError.value = false;
  errorMessage.value = "";

  try {
    const responses = await Promise.all([
      getProfile(),
      getSkills(),
      getAllProjects(),
      getAllCertificates(),
      getAllExperiences(),
    ]);

    for (const res of responses) {
      if (!res.ok) throw new Error(`Failed to fetch data (Status: ${res.status})`);
    }

    const [profileJson, skillJson, projectJson, certificateJson, experienceJson] = await Promise.all(
      responses.map((res) => res.json()),
    );

    profileData.value = profileJson.data || profileJson;
    skillData.value = skillJson.data || skillJson;
    projectData.value = projectJson.data || projectJson;
    certificateData.value = certificateJson.data || certificateJson;
    experienceData.value = experienceJson.data || experienceJson;

    if (profileData.value?.about?.photo_url) {
      await preloadImage(profileData.value.about.photo_url);
    }

    setTimeout(() => {
      isLoading.value = false;
    }, 500);
  } catch (e) {
    console.error("Error loading data:", e);
    isLoading.value = false;
    isError.value = true;
    errorMessage.value = "An error occurred while fetching data. Please ensure the server is running.";
  }
}

// --- 3. Fungsi Init Animasi Stacking ---
function initStackingAnimation() {
  // Pastikan element sudah ada di DOM
  if (projectSectionRef.value && certificateSectionRef.value) {
    gsap.set([projectSectionRef.value.$el, certificateSectionRef.value.$el], {
      willChange: "transform, position",
    });
    // Pastikan Certificate berada di atas Project secara visual (z-index)
    // Project akan diam, Certificate akan jalan di atasnya
    gsap.set(certificateSectionRef.value.$el, {
      position: "relative",
      zIndex: 10,
    });

    scrollTriggerInstance = ScrollTrigger.create({
      trigger: projectSectionRef.value.$el, // Element yang memicu (FeaturedProject)
      start: "bottom bottom", // Mulai saat bagian atas Project menyentuh atas layar
      end: "bottom top", // Selesai saat bagian bawah Project menyentuh atas layar
      pin: true, // Tahan (Freeze) posisi Project
      pinSpacing: false, // PENTING: Jangan beri jarak, biarkan elemen bawah (Certificate) naik menutupi
      anticipatePin: 1,
      pinType: "fixed", // Memaksa penggunaan position: fixed agar tidak bergetar
      fastScrollEnd: true, // Mencegah lonjakan posisi saat scroll cepat
      markers: false, // Ubah ke true jika ingin melihat debug markers
      onUpdate: (self) => {
        // Opsional: Bisa tambah efek opacity atau scale jika mau lebih smooth
      },
    });
  }
}

// --- 4. Watcher untuk Inisialisasi Animasi ---
// Kita harus menunggu sampai isLoading false (DOM dirender) sebelum pasang animasi
watch(isLoading, (newVal) => {
  if (!newVal && !isError.value) {
    nextTick(() => {
      initStackingAnimation();
      // Refresh ScrollTrigger untuk memastikan perhitungan posisi akurat setelah render
      ScrollTrigger.refresh();
    });
  }
});

onMounted(() => {
  fetchAllData();
});

// Bersihkan ScrollTrigger saat komponen di-destroy agar tidak memory leak
onUnmounted(() => {
  if (scrollTriggerInstance) {
    scrollTriggerInstance.kill();
  }
  ScrollTrigger.getAll().forEach((t) => t.kill());
});
</script>

<template>
  <div
    class="min-h-screen bg-white text-black font-sans selection:bg-black selection:text-white overflow-x-hidden flex flex-col pb-24 md:pb-0">
    <Transition name="fade">
      <LoadingScreen v-if="isLoading" />
    </Transition>

    <div
      v-if="!isLoading && isError"
      class="flex flex-col items-center justify-center min-h-screen text-center px-4 animate-in">
      <h2 class="text-2xl font-bold mb-2">Unable to Load Data</h2>

      <p class="text-gray-600 mb-6">{{ errorMessage }}</p>

      <button
        @click="fetchAllData"
        class="px-6 py-2 bg-black text-white rounded-full hover:bg-gray-800 transition-colors duration-300">
        Try Again
      </button>
    </div>

    <div v-if="!isLoading && !isError" class="animate-in">
      <Navbar />

      <Hero :profile="profileData" />
      <Tech :skills="skillData" />

      <FeaturedProject ref="projectSectionRef" :projects="projectData" class="relative z-0" />

      <FeaturedCertificate ref="certificateSectionRef" :certificates="certificateData" class="relative z-10 bg-white" />

      <Experience :experiences="experienceData" />

      <HaveAnIdea />
      <Footer />
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

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
