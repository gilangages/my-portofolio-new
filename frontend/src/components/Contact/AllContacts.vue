<script setup>
import { onMounted, ref, nextTick, watch } from "vue";
import LoadingScreen from "../LoadingScreen.vue";
import { alertError } from "../lib/alert";
import { getAllContacts } from "../lib/api/ContactApi";
import { Icon } from "@iconify/vue";
import gsap from "gsap";

const contacts = ref([]);
const isLoading = ref(true);

// --- Fetch Data ---
async function fetchContacts() {
  isLoading.value = true;
  try {
    const response = await getAllContacts();
    const responseBody = await response.json();

    if (response.status === 200) {
      contacts.value = responseBody.data || responseBody;
    } else {
      await alertError(responseBody.message);
    }
  } catch (e) {
    console.error(`Error fetch contacts:`, e);
  } finally {
    // Delay buatan agar transisi smooth
    setTimeout(() => {
      isLoading.value = false;
    }, 800);
  }
}

// --- GSAP Animation Trigger ---
watch(isLoading, (newVal) => {
  if (!newVal) {
    nextTick(() => {
      animateEntrance();
    });
  }
});

// --- GSAP Animation Logic ---
const animateEntrance = () => {
  const tl = gsap.timeline();

  // Animasi Judul
  tl.fromTo(
    ".comic-title",
    { y: -100, autoAlpha: 0, scale: 2 },
    {
      y: 0,
      autoAlpha: 1,
      scale: 1,
      duration: 0.8,
      ease: "bounce.out",
    },
  );

  // Animasi Panel Kartu (Social Media Cards)
  if (contacts.value.length > 0) {
    tl.fromTo(
      ".comic-panel",
      {
        y: 50,
        autoAlpha: 0, // Mulai dari invisible
        rotation: -5,
        scale: 0.8,
      },
      {
        y: 0,
        autoAlpha: 1, // Menjadi visible
        rotation: 0,
        scale: 1,
        duration: 0.5,
        ease: "back.out(1.5)",
        stagger: 0.1,
        clearProps: "all",
      },
      "-=0.4", // Overlap dengan judul
    );
  }
};

onMounted(async () => {
  await fetchContacts();
});
</script>

<template>
  <div class="-mt-20 md:mt-0 comic-container min-h-screen relative overflow-x-hidden text-black font-sans">
    <div
      class="absolute inset-0 z-0 opacity-10 pointer-events-none"
      style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 20px 20px"></div>

    <Transition name="fade">
      <LoadingScreen v-if="isLoading" />
    </Transition>

    <div v-if="!isLoading" class="container mx-auto px-6 py-24 md:py-26 relative z-10">
      <div class="mb-20 text-center">
        <div
          class="inline-block bg-black text-white px-4 py-1 font-bold uppercase tracking-widest text-sm mb-4 transform -rotate-2 border-2 border-white shadow-lg">
          Connect With Me
        </div>

        <h1
          class="comic-title text-6xl md:text-8xl font-black uppercase tracking-tighter leading-none drop-shadow-[4px_4px_0px_rgba(0,0,0,0.2)]"
          style="opacity: 0; visibility: hidden">
          GET IN
          <br />
          <span class="text-transparent bg-clip-text bg-black text-stroke-white">TOUCH</span>
        </h1>

        <p
          class="mt-6 text-xl font-bold italic bg-white inline-block px-6 py-2 border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
          "Select your transmission frequency below."
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-4">
        <div
          v-for="contact in contacts"
          :key="contact.id"
          class="comic-panel group relative bg-white border-4 border-black p-0 transition-all duration-300 hover:-translate-y-2 hover:translate-x-1"
          style="opacity: 0; visibility: hidden">
          <div
            class="absolute inset-0 bg-black translate-x-2 translate-y-2 -z-10 transition-transform duration-300 group-hover:translate-x-4 group-hover:translate-y-4"></div>

          <div class="flex flex-col h-full">
            <div
              class="p-8 flex flex-col items-center justify-center border-b-4 border-black bg-white flex-grow relative overflow-hidden">
              <div
                class="relative w-24 h-24 bg-white border-4 border-black rounded-full flex items-center justify-center mb-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] group-hover:scale-110 transition-transform duration-300">
                <Icon v-if="contact.icon" :icon="contact.icon" class="w-12 h-12 text-black" />
              </div>

              <h2 class="text-3xl font-black uppercase tracking-tight text-center z-10 bg-white px-2">
                {{ contact.platform_name }}
              </h2>
            </div>

            <div class="p-4 bg-white">
              <a
                :href="contact.url"
                target="_blank"
                class="block w-full text-center bg-black text-white font-bold py-4 text-lg border-2 border-transparent uppercase tracking-wider hover:bg-white hover:text-black hover:border-black transition-colors duration-300 flex items-center justify-center gap-2">
                <span>OPEN LINK</span>
                <Icon icon="mdi:arrow-right-bold" class="w-5 h-5" />
              </a>
            </div>
          </div>
        </div>
      </div>

      <div
        v-if="!isLoading && contacts.length === 0"
        class="text-center py-20 bg-white border-4 border-black border-dashed">
        <Icon icon="mdi:signal-off" class="text-6xl mx-auto mb-4" />
        <p class="text-2xl font-black uppercase">NO SIGNAL DETECTED.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Utility untuk efek outline text pada judul */
.text-stroke-white {
  -webkit-text-stroke: 3px transparent;
  -webkit-text-stroke: 2px white;
  color: transparent;
}

/* Background utama: Diganti jadi Putih Polos */
.comic-container {
  background-color: #ffffff;
}

/* Transisi Vue */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.6s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
