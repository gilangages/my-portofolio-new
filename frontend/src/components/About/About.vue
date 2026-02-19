<script setup>
import { onMounted, ref, computed, nextTick, watch } from "vue";
import gsap from "gsap";
import { getProfile } from "../../components/lib/api/ProfileApi";
import LoadingScreen from "../../components/LoadingScreen.vue";
import Navbar from "../Navbar.vue";

// --- STATE ---
const profile = ref({});
const isLoading = ref(true);

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
    });
  }
});

// Data Hobbies (Statis)
const hobbies = [
  {
    title: "Digital Art & Doodles",
    desc: "Translating imagination onto a digital canvas. I occasionally share my casual doodles and character arts on my Instagram",
    link: "https://instagram.com/qeynotfound",
    linkText: "@qeynotfound",
  },
  {
    title: "Acoustic Guitar",
    desc: "Strumming chords serves as my favorite way to recalibrate my mind and find rhythm after hours of coding.",
  },
  {
    title: "Books & Comics",
    desc: "Immersing myself in compelling narratives and visual storytelling through literature and graphic novels.",
  },
  {
    title: "Culinary Experiments",
    desc: "Perfecting my signature fried rice recipe—my ultimate late-night comfort routine when stepping away from the keyboard.",
  },
];
</script>

<template>
  <Transition name="fade">
    <LoadingScreen v-if="isLoading" />
  </Transition>

  <section
    v-if="!isLoading"
    class="min-h-screen flex justify-center py-20 px-6 bg-white font-sans text-black selection:bg-black selection:text-white">
    <div class="container max-w-6xl w-full">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start mt-10 md:mt-16">
        <div class="lg:col-span-5 flex flex-col gap-4 lg:sticky lg:top-24">
          <div
            class="anim-img relative overflow-hidden aspect-[4/5] bg-white border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
            <img
              v-if="aboutData.secondary_image_url"
              :src="aboutData.secondary_image_url"
              alt="Personal Portrait"
              class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700" />
          </div>
          <p class="anim-img text-xs font-bold uppercase tracking-widest text-right mt-2 text-black">
            // STATUS: UNIVERSITY STUDENT & DEVELOPER
          </p>
        </div>

        <div class="lg:col-span-7 flex flex-col space-y-16">
          <div class="space-y-6">
            <div class="space-y-4">
              <div
                class="anim-text inline-block border-2 border-black px-3 py-1 bg-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <h2 class="text-xs font-bold uppercase tracking-[0.2em] text-black">Introduction</h2>
              </div>
              <h1 class="anim-text text-5xl md:text-6xl font-black tracking-tight leading-tight uppercase">
                Behind The
                <br />
                <span class="text-white text-shadow-comic">Source Code.</span>
              </h1>
            </div>

            <div class="anim-text space-y-5 text-lg text-black leading-relaxed font-medium">
              <p>
                Hello, I am Gilang Abdian Anggara, a Diploma (D3) student in Informatics Engineering at Universitas
                Sebelas Maret. As a full-stack developer, I focus on building web applications that are reliable under
                the hood and visually comforting on the screen.
              </p>
              <p>
                I believe that good software is not just about making things work, but about creating seamless
                experiences. I blend strict engineering logic with a relaxing, lo-fi aesthetic so that complex
                applications feel friendly and easy to use.
              </p>
            </div>
          </div>

          <div class="anim-text w-full h-1 bg-black"></div>

          <div class="anim-box space-y-6">
            <h3 class="text-sm font-black uppercase tracking-[0.1em] text-black">The Architecture of Comfort</h3>
            <p class="text-black font-medium leading-relaxed">
              When developing a project, my main goal is to create products that are easy to use, easy to scale, and
              easy to maintain. My core development principles include:
            </p>

            <ul class="space-y-4 text-black font-medium leading-relaxed border-l-4 border-black pl-4">
              <li>
                <strong>Clean Code & Maintenance:</strong>
                Writing clean, modular, and readable code. A well-organized codebase is the key to preventing future
                bugs and making long-term maintenance much easier for the team.
              </li>
              <li>
                <strong>RESTful APIs & Backend:</strong>
                Building solid and secure backend systems. I design efficient RESTful APIs and optimized databases to
                ensure data flows smoothly and safely between the server and the client.
              </li>
              <li>
                <strong>User-Friendly Interfaces:</strong>
                Designing responsive and accessible frontend layouts. I focus on translating complex systems into clean,
                intuitive UI that prioritizes a great user experience.
              </li>
            </ul>

            <p class="text-black font-medium leading-relaxed pt-2">
              Whether I am setting up a database or styling a web page, my ultimate goal is to turn technical
              requirements into a product that users actually enjoy using.
            </p>
          </div>

          <div class="anim-text w-full h-1 bg-black"></div>

          <div class="anim-box space-y-8">
            <div class="space-y-2">
              <div class="inline-block border-2 border-black px-3 py-1 bg-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-black">Off The Grid</h3>
              </div>
              <p class="text-black font-medium text-sm mt-3">
                Activities that provide balance and clarity when I step away from the IDE.
              </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div
                v-for="(hobby, index) in hobbies"
                :key="index"
                class="p-5 bg-white border-2 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[2px] hover:translate-x-[2px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all duration-200">
                <div class="flex items-center gap-3 mb-3 border-b-2 border-black pb-2">
                  <div class="w-3 h-3 bg-black"></div>
                  <h4 class="font-black text-black text-sm tracking-wide uppercase">{{ hobby.title }}</h4>
                </div>
                <p class="text-sm text-black font-medium leading-relaxed">
                  {{ hobby.desc }}
                  <a
                    v-if="hobby.link"
                    :href="hobby.link"
                    target="_blank"
                    class="font-black underline decoration-2 decoration-black hover:bg-black hover:text-white transition-colors">
                    {{ hobby.linkText }}
                  </a>
                  .
                </p>
              </div>
            </div>
          </div>

          <div class="anim-box pt-8 pb-10">
            <router-link
              to="/contacts"
              class="inline-block px-8 py-4 bg-black text-white text-sm font-black uppercase tracking-widest border-2 border-black shadow-[6px_6px_0px_0px_rgba(200,200,200,1)] hover:bg-white hover:text-black hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-1 transition-all duration-300">
              Initiate Contact
            </router-link>
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

/* Custom Text Shadow for Comic Effect */
.text-shadow-comic {
  text-shadow:
    -2px -2px 0 #000,
    2px -2px 0 #000,
    -2px 2px 0 #000,
    2px 2px 0 #000,
    4px 4px 0px #000;
}
</style>
