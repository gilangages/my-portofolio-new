<script setup>
import { onMounted, ref, computed, nextTick, watch } from "vue";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger"; // 1. WAJIB TAMBAH INI
import { getProfile } from "../../lib/api/ProfileApi";
import LoadingScreen from "../../components/LoadingScreen.vue";

// --- STATE ---
const profile = ref({});
const isLoading = ref(true);

// --- COMPUTED ---
const aboutData = computed(() => profile.value.about || {});
gsap.registerPlugin(ScrollTrigger);
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

// --- LIFECYCLE ---
onMounted(async () => {
  await fetchProfileData();
});

// Animation trigger when loading finishes
watch(isLoading, (newVal) => {
  if (!newVal) {
    nextTick(() => {
      const tl = gsap.timeline({
        // TAMBAHKAN ONCOMPLETE UNTUK REFRESH
        onComplete: () => {
          ScrollTrigger.refresh();
        },
      });

      tl.from(".anim-img", {
        scale: 1.05,
        opacity: 0,
        duration: 1.2,
        ease: "power3.out",
      })
        .from(
          ".anim-text",
          {
            y: 20,
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
            stagger: 0.15,
            ease: "power2.out",
          },
          "-=0.6",
        );
      // Paksa refresh sekali lagi di awal konten muncul
      ScrollTrigger.refresh();
    });
  }
});

// Data Hobbies (Statis) - Updated to Evergreen English & added Icons
const hobbies = [
  {
    title: "Drawing",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>`,
    desc: "Sometimes I also draw. I mostly draw fan art traditionally, and I have also started drawing digitally. If you're curious about my drawings, you can check them out on my Instagram.",
    link: "https://instagram.com/qeynotfound",
    linkText: "@qeynotfound",
  },
  {
    title: "Playing Guitar",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>`,
    desc: "Sometimes I play the guitar and sing my favorite songs. It helps me relax and enjoy my free time.",
  },
  {
    title: "Read Coomics",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>`,
    desc: "Sometimes I read comics to improve my empathy and to spend my free time. It makes me feel better than anything else.",
  },
  {
    title: "Cooking",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/></svg>`,
    desc: "I enjoy cooking in my free time. Fried rice is my favorite to make, and I'm still learning to cook other dishes.",
  },
];
</script>

<template>
  <Transition name="fade">
    <LoadingScreen v-if="isLoading" />
  </Transition>

  <section
    v-if="!isLoading"
    class="-mt-30 md:-mt-12 min-h-screen flex justify-center py-24 px-4 sm:px-6 bg-white font-sans text-black selection:bg-black selection:text-white">
    <div class="container max-w-5xl w-full flex flex-col space-y-12">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch mt-10">
        <div class="md:col-span-4 anim-img">
          <div class="comic-panel h-full flex flex-col p-3 bg-white">
            <div class="relative w-full aspect-[4/5] border-2 border-black overflow-hidden flex-grow bg-gray-100">
              <img
                v-if="aboutData.secondary_image_url"
                :src="aboutData.secondary_image_url"
                alt="Personal Portrait"
                class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700" />
            </div>
            <div class="mt-3 border-t-2 border-black pt-2 text-center">
              <p class="text-xs font-black uppercase tracking-widest text-black">// Developer</p>
            </div>
          </div>
        </div>

        <div class="md:col-span-8 anim-box flex flex-col justify-center">
          <div class="comic-panel h-full flex flex-col justify-center p-8 space-y-6 bg-white">
            <div class="space-y-4">
              <h1
                class="anim-text text-5xl md:text-6xl lg:text-7xl font-black tracking-tight leading-none uppercase text-shadow-comic text-white">
                About Me.
              </h1>
            </div>

            <div class="anim-text space-y-4 text-base md:text-lg text-black font-medium leading-relaxed">
              <p>
                Hello, I am Gilang Abdian Anggara. As a dedicated Full-Stack Developer, I build web applications from
                the ground up—handling everything from complex database architectures to the user-facing interface.
              </p>
              <p>
                I focus on creating software that is functional, scalable, and easy to maintain. My goal is to blend
                solid engineering principles with a clean, relaxing aesthetic, ensuring that even the most complex
                systems feel simple and approachable.
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="anim-box comic-panel p-8 space-y-8 bg-white">
        <div class="flex items-center gap-4 border-b-4 border-black pb-4">
          <h3 class="text-2xl md:text-3xl font-black uppercase tracking-wide">What I Do</h3>
          <span class="flex-grow h-2 bg-black"></span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div
            class="border-2 border-black p-5 hover:-translate-y-1 hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-200">
            <h4 class="font-black text-lg uppercase mb-2">Frontend Interface</h4>
            <p class="text-sm font-medium leading-relaxed">
              Designing responsive and accessible interfaces. I translate ideas into clean, neat UIs that prioritize a
              seamless user experience.
            </p>
          </div>
          <div
            class="border-2 border-black p-5 hover:-translate-y-1 hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-200">
            <h4 class="font-black text-lg uppercase mb-2">Backend & APIs</h4>
            <p class="text-sm font-medium leading-relaxed">
              Building secure server architectures. I design efficient database structures and RESTful APIs to ensure
              smooth data flow.
            </p>
          </div>
          <div
            class="border-2 border-black p-5 hover:-translate-y-1 hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-200">
            <h4 class="font-black text-lg uppercase mb-2">Clean Codebase</h4>
            <p class="text-sm font-medium leading-relaxed">
              Writing readable and modular code. A well-organized project structure is essential for preventing bugs and
              simplifying future updates.
            </p>
          </div>
          <div
            class="border-2 border-black p-5 hover:-translate-y-1 hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-200">
            <h4 class="font-black text-lg uppercase mb-2">System Deployment</h4>
            <p class="text-sm font-medium leading-relaxed">
              Handling the application deployment process to ensure it runs stably in production environments and
              remains highly accessible.
            </p>
          </div>
        </div>
      </div>

      <div class="anim-box space-y-4">
        <div class="inline-block border-2 border-black px-4 py-2 bg-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
          <h3 class="text-sm font-black uppercase tracking-[0.15em]">Life Outside the Code</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 border-4 border-black p-2 bg-black">
          <div
            v-for="(hobby, index) in hobbies"
            :key="index"
            class="bg-white p-5 flex flex-col border-2 border-black hover:scale-[1.02] transition-transform duration-300">
            <div class="mb-3 border-b-2 border-gray-200 pb-3 text-black" v-html="hobby.icon"></div>

            <h4 class="font-black text-base uppercase leading-tight mb-3">{{ hobby.title }}</h4>
            <p class="text-sm font-medium leading-relaxed flex-grow text-gray-800">
              {{ hobby.desc }}
            </p>
            <a
              v-if="hobby.link"
              :href="hobby.link"
              target="_blank"
              class="mt-4 text-xs font-black uppercase tracking-widest underline decoration-2 hover:bg-black hover:text-white px-2 py-1 transition-colors self-start">
              {{ hobby.linkText }}
            </a>
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

/* CSS Panel Komik Reusable */
.comic-panel {
  border: 4px solid #000;
  box-shadow: 8px 8px 0px 0px rgba(0, 0, 0, 1);
}

/* Custom Text Shadow for Comic Title Effect */
.text-shadow-comic {
  text-shadow:
    -2px -2px 0 #000,
    2px -2px 0 #000,
    -2px 2px 0 #000,
    2px 2px 0 #000,
    5px 5px 0px #000;
}
</style>
