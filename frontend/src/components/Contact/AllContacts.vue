<script setup>
import { onMounted, ref, nextTick, watch } from "vue";
import LoadingScreen from "../LoadingScreen.vue";
import { alertError } from "../../lib/alert";
import { getAllContacts } from "../../lib/api/ContactApi";
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
    setTimeout(() => {
      isLoading.value = false;
      window.dispatchEvent(new CustomEvent("content-loaded"));
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

  tl.fromTo(
    ".comic-title",
    { y: 20, autoAlpha: 0 },
    {
      y: 0,
      autoAlpha: 1,
      duration: 0.6,
      ease: "back.out(1.5)",
    },
  );

  if (contacts.value.length > 0) {
    tl.fromTo(
      ".comic-panel",
      {
        y: 20,
        autoAlpha: 0,
      },
      {
        y: 0,
        autoAlpha: 1,
        duration: 0.5,
        ease: "power2.out",
        stagger: 0.05,
        clearProps: "all",
      },
      "-=0.3",
    );
  }
};

onMounted(async () => {
  await fetchContacts();
});
</script>

<template>
  <div
    class="-mt-16 mb-40 md:-mt-5 comic-container min-h-screen relative overflow-x-hidden text-black font-sans pb-60 md:pb-30">
    <Transition name="fade">
      <LoadingScreen v-if="isLoading" />
    </Transition>

    <div v-if="!isLoading" class="container mx-auto px-6 py-20 md:py-28 relative z-10 max-w-2xl">
      <div class="mb-12 text-center comic-title" style="opacity: 0; visibility: hidden">
        <h1
          class="text-3xl md:text-5xl font-black font-serif uppercase tracking-wider inline-block relative border-b border-black/20 pb-2">
          <span class="relative z-10">Contacts</span>
          <span class="absolute top-0 left-0 w-full h-full bg-gray-200 -z-0 -rotate-1 skew-x-12 opacity-70"></span>
        </h1>
        <p class="mt-4 font-[Inter] text-gray-600 text-sm md:text-base max-w-xl mx-auto italic">
          "Let's connect."
        </p>
      </div>

      <div class="flex flex-col mt-4">
        <div class="border-t border-black/20 w-full mb-2"></div>

        <a v-for="contact in contacts" :key="contact.id" :href="contact.url" target="_blank"
          class="comic-panel group flex items-center justify-between py-4 px-2 md:px-4 border-b border-black/10 transition-all duration-300 hover:bg-black/5 cursor-pointer"
          style="opacity: 0; visibility: hidden">
          <div class="flex items-center gap-4 md:gap-5">
            <Icon v-if="contact.icon" :icon="contact.icon"
              class="w-6 h-6 md:w-8 md:h-8 transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-12" />
            <h2 class="text-xl md:text-2xl font-bold uppercase tracking-wide m-0">
              {{ contact.platform_name }}
            </h2>
          </div>

          <Icon icon="mdi:arrow-top-right-thick"
            class="w-5 h-5 md:w-6 md:h-6 opacity-0 -translate-x-2 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0" />
        </a>
      </div>

      <div v-if="!isLoading && contacts.length === 0"
        class="text-center py-10 bg-gray-50 border border-black/20 border-dashed mt-8 rounded-lg shadow-sm">
        <Icon icon="mdi:signal-off" class="text-4xl mx-auto mb-2" />
        <p class="text-lg font-bold uppercase tracking-wide">NO SIGNAL DETECTED.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.comic-container {
  background-color: #ffffff;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.6s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>