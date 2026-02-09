<script setup>
import { Icon } from "@iconify/vue";
import Navbar from "./Section/Navbar.vue";
import { getProfile } from "../lib/api/ProfileApi";
import { onMounted, ref } from "vue";
import { alertError } from "../lib/alert";

const profile = ref(null);
const storageUrl = `${import.meta.env.VITE_STORAGE_URL}`;

// FUNGSI BARU: Mendeteksi apakah link sudah HTTPS (Cloudinary) atau butuh storageUrl (Local)
const getFullUrl = (path) => {
  if (!path) return "";
  if (path.startsWith("http://") || path.startsWith("https://")) {
    return path;
  }
  return `${storageUrl}${path}`;
};

async function fetchProfile() {
  const response = await getProfile();
  const responseBody = await response.json();
  console.log(responseBody);
  if (response.status === 200) {
    profile.value = responseBody;
  } else {
    await alertError(responseBody.message);
  }
}

onMounted(async () => {
  await fetchProfile();
});
</script>

<template>
  <div
    v-if="profile && profile.about"
    class="min-h-screen bg-white text-black font-sans selection:bg-black selection:text-white overflow-x-hidden flex flex-col pb-32 md:pb-0">
    <Navbar />
    <main class="container mx-auto px-6 pt-24 pb-6 md:pt-8 md:pb-0 min-h-screen flex items-center justify-center">
      <div class="flex flex-col-reverse md:flex-row items-center justify-between w-full max-w-4xl gap-8 md:gap-2 mt-8">
        <div class="flex-1 flex flex-col items-start space-y-3 md:space-y-3 mt-4">
          <div
            class="inline-block bg-gray-200 border-2 border-black px-3 py-1 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transform -rotate-1">
            <h5 class="font-bold text-[10px] md:text-xs tracking-wide uppercase">Available for Work</h5>
          </div>

          <div class="space-y-0.5">
            <h1 class="text-4xl md:text-5xl font-bold leading-tight font-[Inter] tracking-tight">
              Hi, I'm
              <br class="hidden md:block" />
              <span class="underline decoration-4 underline-offset-4 decoration-black">{{ profile.about.name }}</span>
            </h1>
            <h2 class="text-xl md:text-2xl lg:text-3xl font-[Playfair_Display] italic text-gray-800 pt-1">
              {{ profile.about.job_title }}
            </h2>
          </div>

          <p class="text-sm text-justify leading-relaxed max-w-lg font-[Inter] text-gray-700">
            {{ profile.about.about_description }}
          </p>

          <div class="flex flex-wrap gap-2 font-medium text-xs">
            <div class="flex items-center gap-1.5 py-1">
              <Icon icon="mdi:map-marker" />
              <span>Based in Indonesia</span>
            </div>
            <div class="flex items-center gap-1.5 px-2.5 py-1 text-black">
              <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
              <span>Available Now</span>
            </div>
          </div>

          <div class="flex gap-3 pt-1 w-full md:w-auto">
            <button
              class="flex-1 md:flex-none flex items-center justify-center gap-2 bg-black text-white px-5 py-2 rounded-xl border-2 border-black font-bold text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-white hover:text-black hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[3px] active:translate-y-[3px] transition-all">
              <Icon icon="mdi:handshake-outline" class="w-4 h-4 md:w-5 md:h-5" />
              <span>Hire Me</span>
            </button>

            <a
              :href="getFullUrl(profile.about.cv_path)"
              target="_blank"
              class="flex-1 md:flex-none flex items-center justify-center gap-2 bg-white text-black px-5 py-2 rounded-xl border-2 border-black font-bold text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-gray-100 hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[3px] active:translate-y-[3px] transition-all">
              <Icon icon="mdi:file-download-outline" class="w-4 h-4 md:w-5 md:h-5" />
              <span>Download CV</span>
            </a>
          </div>

          <hr class="w-full border-t-2 border-gray-400 my-2" />

          <div class="flex flex-col md:flex-row items-start md:items-center gap-2 text-xs font-bold">
            <span class="whitespace-nowrap">Follow me:</span>
            <div class="flex flex-wrap gap-2">
              <a
                v-for="social in profile.social_media"
                :key="social.name"
                :href="social.url"
                target="_blank"
                class="p-1.5 border-2 border-black rounded-lg hover:bg-black hover:text-white transition-colors duration-200"
                :title="social.name">
                <Icon :icon="social.icon" class="w-4 h-4" />
              </a>
            </div>
          </div>
        </div>

        <div class="w-full md:w-5/12 flex justify-center md:justify-end relative">
          <div class="absolute inset-0 bg-gray-100 rounded-full scale-90 blur-3xl -z-10 opacity-50"></div>

          <img
            :src="getFullUrl(profile.about.photo_path)"
            alt="Gilang Abdian"
            class="w-[400px] md:w-[300px] -mt-32 md:mt-0 h-auto object-cover grayscale contrast-110 border-b-4 border-black" />
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
.font-serif {
  font-family: "DM Serif Display", serif;
}
</style>
