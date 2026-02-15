<script setup>
import { onMounted, ref, nextTick } from "vue";
import LoadingScreen from "../LoadingScreen.vue"; // Import LoadingScreen
import { alertError } from "../lib/alert";
import { getAllContacts } from "../lib/api/ContactApi";
import { Icon } from "@iconify/vue";
import gsap from "gsap";

const contacts = ref([]);
const isLoading = ref(true);

// Fungsi Fetch Data
async function fetchContacts() {
  isLoading.value = true;
  try {
    const response = await getAllContacts();
    const responseBody = await response.json();
    console.log(responseBody);

    if (response.status === 200) {
      contacts.value = responseBody.data || responseBody;
    } else {
      await alertError(responseBody.message);
    }
  } catch (e) {
    console.error(`Error fetch contacts:`, e);
  } finally {
    // 1. Matikan loading state dulu agar elemen v-else dirender
    isLoading.value = false;

    // 2. Tunggu DOM selesai update (agar class .contact-card sudah ada)
    await nextTick();

    // 3. Baru jalankan animasi masuk
    animateEntrance();
  }
}

// Fungsi Animasi GSAP
const animateEntrance = () => {
  const tl = gsap.timeline();

  // Animasi Header (Judul)
  tl.from(".contact-header", {
    y: -50,
    opacity: 0,
    duration: 0.8,
    ease: "power4.out",
    stagger: 0.2,
  });

  // Animasi Kartu Kontak
  tl.from(
    ".contact-card",
    {
      y: 100, // Muncul dari bawah
      opacity: 0, // Dari transparan
      scale: 0.9, // Dari agak kecil
      rotation: 3, // Sedikit miring
      duration: 0.6,
      ease: "back.out(1.7)", // Efek membal (bouncy)
      stagger: 0.15, // Muncul bergantian
    },
    "-=0.5", // Mulai sedikit lebih awal sebelum header selesai
  );
};

onMounted(async () => {
  await fetchContacts();
});
</script>

<template>
  <div class="min-h-screen bg-[#f5f5f5] font-sans text-black relative overflow-x-hidden">
    <Transition name="fade">
      <LoadingScreen v-if="isLoading" />
    </Transition>

    <div class="container mx-auto px-6 py-24 md:py-32">
      <div class="mb-16 text-center relative z-10">
        <div
          class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-24 bg-gray-300 rotate-3 rounded-full blur-2xl -z-10 opacity-50"></div>

        <h1 class="contact-header text-5xl md:text-7xl font-black uppercase tracking-tighter mb-4 drop-shadow-sm">
          Communication
          <br />
          <span class="text-stroke-2 text-transparent bg-clip-text bg-black">Channels</span>
        </h1>
        <p class="contact-header font-[Playfair_Display] italic text-xl md:text-2xl text-gray-600">
          "Reach out via these frequencies."
        </p>
      </div>

      <div v-if="!isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="contact in contacts"
          :key="contact.id"
          class="contact-card group relative bg-white border-4 border-black p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] hover:shadow-[0px_0px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[6px] hover:translate-y-[6px] transition-all duration-300 cursor-default">
          <div class="absolute top-2 left-2 w-1.5 h-1.5 bg-black rounded-full"></div>
          <div class="absolute top-2 right-2 w-1.5 h-1.5 bg-black rounded-full"></div>
          <div class="absolute bottom-2 left-2 w-1.5 h-1.5 bg-black rounded-full"></div>
          <div class="absolute bottom-2 right-2 w-1.5 h-1.5 bg-black rounded-full"></div>

          <div class="flex flex-col items-center text-center space-y-4">
            <div
              class="w-20 h-20 bg-black text-white flex items-center justify-center rounded-full border-4 border-black group-hover:bg-white group-hover:text-black transition-colors duration-300">
              <Icon :icon="contact.icon" class="w-10 h-10" />
            </div>

            <h2 class="text-2xl font-black uppercase tracking-tight border-b-4 border-black pb-2 w-full">
              {{ contact.platform_name }}
            </h2>

            <a
              :href="contact.url"
              target="_blank"
              class="w-full block bg-white border-2 border-black py-3 font-bold uppercase tracking-wider text-sm hover:bg-black hover:text-white transition-all flex items-center justify-center gap-2 group/btn shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[2px] active:translate-y-[2px]">
              <span>Connect</span>
              <Icon
                icon="mdi:arrow-top-right"
                class="w-4 h-4 group-hover/btn:translate-x-1 group-hover/btn:-translate-y-1 transition-transform" />
            </a>

            <p class="text-[10px] font-mono text-gray-500 truncate w-full px-2 opacity-60">
              {{ contact.url }}
            </p>
          </div>
        </div>
      </div>

      <div
        v-if="!isLoading && contacts.length === 0"
        class="text-center py-20 border-4 border-dashed border-gray-400 rounded-xl bg-white/50">
        <Icon icon="mdi:signal-off" class="text-4xl text-gray-400 mb-2 mx-auto" />
        <p class="font-bold text-gray-500 text-xl">No frequencies found.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Utility Custom */
.text-stroke-2 {
  -webkit-text-stroke: 2px black;
}

/* Transisi Fade untuk Loading Screen */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
