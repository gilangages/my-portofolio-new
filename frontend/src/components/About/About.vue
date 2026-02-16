<script setup>
import { onMounted, ref, computed, nextTick, watch } from "vue";
import gsap from "gsap";
import { getProfile } from "../../components/lib/api/ProfileApi";
import LoadingScreen from "../../components/LoadingScreen.vue";
import Navbar from "../Navbar.vue";

// --- STATE ---
const profile = ref({});
const isLoading = ref(true);

// State for "Girlfriend vs Hobbies" feature
const isHobbyRevealed = ref(false);
const buttonLabel = ref("Girlfriend?");
const hobbyContainer = ref(null);

// Hobbies Data (Text only, no icons)
const hobbies = ["Drawing (Doodle/Chibi)", "Playing Acoustic Guitar", "Reading Books", "Cooking Fried Rice (Expert!)"];

// --- COMPUTED ---
const aboutData = computed(() => profile.value.about || {});

// --- FUNCTION ---
const fetchProfileData = async () => {
  isLoading.value = true;
  try {
    const res = await getProfile();
    if (!res.ok) throw new Error("Failed to fetch");
    const json = await res.json();
    profile.value = json.data || json;
  } catch (error) {
    console.error("Error fetching profile:", error);
  } finally {
    setTimeout(() => {
      isLoading.value = false;
    }, 800);
  }
};

const revealHobby = () => {
  if (isHobbyRevealed.value) return;

  isHobbyRevealed.value = true;
  buttonLabel.value = "Hobbies";

  // Subtle button animation
  gsap.to(".btn-interaction", {
    scale: 1.05,
    duration: 0.2,
    yoyo: true,
    repeat: 1,
    ease: "power2.out",
  });

  // Reveal Animation
  nextTick(() => {
    gsap.fromTo(
      ".hobby-text",
      { y: 10, opacity: 0 },
      { y: 0, opacity: 1, duration: 0.6, stagger: 0.1, ease: "power2.out" },
    );
    gsap.fromTo(
      ".hobby-message",
      { opacity: 0, x: -5 },
      { opacity: 1, x: 0, duration: 0.6, delay: 0.3, ease: "power2.out" },
    );
  });
};

// --- LIFECYCLE ---
onMounted(async () => {
  await fetchProfileData();
});

// Animation trigger when loading finishes
watch(isLoading, (newVal) => {
  if (!newVal) {
    nextTick(() => {
      const tl = gsap.timeline();

      tl.from(".anim-img", {
        scale: 1.1,
        opacity: 0,
        duration: 1.2,
        ease: "power3.out",
      })
        .from(
          ".anim-text",
          {
            y: 30,
            opacity: 0,
            duration: 0.8,
            stagger: 0.1,
            ease: "power2.out",
          },
          "-=0.8",
        )
        .from(
          ".anim-box",
          {
            y: 20,
            opacity: 0,
            duration: 0.8,
            ease: "power2.out",
          },
          "-=0.6",
        );
    });
  }
});
</script>

<template>
  <Transition name="fade">
    <LoadingScreen v-if="isLoading" />
  </Transition>

  <section
    v-if="!isLoading && aboutData.name"
    class="min-h-screen flex items-center justify-center py-20 px-6 bg-[#FAFAFA] font-[Inter] text-gray-900">
    <div class="container max-w-6xl w-full">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-12 items-start">
        <div class="md:col-span-5 flex flex-col gap-4 sticky top-10">
          <div class="anim-img relative overflow-hidden rounded-sm shadow-xl aspect-[3/4]">
            <img
              :src="aboutData.secondary_image_url"
              alt="Portrait"
              class="w-full h-full object-cover transition-transform duration-700 hover:scale-105 grayscale hover:grayscale-0" />
          </div>
          <p class="anim-img text-xs text-gray-400 uppercase tracking-widest text-right mt-2">// Personal Portrait</p>
        </div>

        <div class="md:col-span-7 flex flex-col space-y-10 md:pt-10">
          <div class="space-y-4">
            <h2 class="anim-text text-xs font-bold uppercase tracking-[0.2em] text-gray-500">The Persona</h2>
            <h1 class="anim-text text-5xl md:text-7xl font-light tracking-tight leading-tight">
              About
              <span class="font-bold">Me.</span>
            </h1>
          </div>

          <div class="anim-text space-y-6 text-lg text-gray-600 leading-relaxed font-light">
            <p>
              {{ aboutData.about_description }}
            </p>
          </div>

          <div class="anim-text w-24 h-[1px] bg-gray-300"></div>

          <div class="anim-box bg-white p-8 rounded-sm shadow-sm border border-gray-100">
            <h3 class="text-sm font-bold uppercase tracking-widest mb-6 text-black">Trivia & Interests</h3>

            <div class="flex flex-col items-start gap-6">
              <div class="flex flex-wrap items-center gap-4">
                <button
                  @click="revealHobby"
                  :disabled="isHobbyRevealed"
                  :class="[
                    'btn-interaction px-8 py-3 text-sm font-medium tracking-wide transition-all duration-300 border border-gray-900',
                    isHobbyRevealed
                      ? 'bg-black text-white cursor-default'
                      : 'bg-transparent text-black hover:bg-black hover:text-white',
                  ]">
                  {{ buttonLabel }}
                </button>

                <span v-if="isHobbyRevealed" class="hobby-message text-sm text-gray-500 italic font-light">
                  "Oops! You meant my hobbies, right? 😄"
                </span>
              </div>

              <div v-if="isHobbyRevealed" ref="hobbyContainer" class="w-full pt-4">
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-8">
                  <li
                    v-for="(hobby, index) in hobbies"
                    :key="index"
                    class="hobby-text flex items-baseline gap-3 text-gray-700">
                    <span class="w-1.5 h-1.5 bg-black rounded-full shrink-0"></span>
                    <span class="text-sm md:text-base">{{ hobby }}</span>
                  </li>
                </ul>
              </div>

              <div v-else class="text-xs text-gray-400 mt-2">Click the button above to reveal something *special*.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.6s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

img {
  -webkit-user-drag: none;
}
</style>
