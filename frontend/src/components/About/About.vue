<script setup>
import { onMounted, ref, computed, nextTick, watch } from "vue";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { getProfile } from "../../lib/api/ProfileApi";
import NProgress from "nprogress";
import "nprogress/nprogress.css";

// --- STATE ---
const profile = ref({});
const isLoading = ref(true);

// --- COMPUTED ---
const aboutData = computed(() => profile.value.about || {});
gsap.registerPlugin(ScrollTrigger);

// --- FUNCTION ---
const fetchProfileData = async () => {
  isLoading.value = true;
  NProgress.start();
  try {
    const res = await getProfile();
    if (!res.ok) throw new Error("Failed to fetch");
    const json = await res.json();
    profile.value = json.data || json;
  } catch (error) {
    console.error("Error fetching profile:", error);
  } finally {
    NProgress.done();
    setTimeout(() => {
      isLoading.value = false;
      window.dispatchEvent(new CustomEvent("content-loaded"));
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
        onComplete: () => {
          ScrollTrigger.refresh();
        },
      });

      tl.from(
        ".anim-text",
        {
          y: 20,
          opacity: 0,
          duration: 0.8,
          stagger: 0.1,
          ease: "power2.out",
        }
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
      ScrollTrigger.refresh();
    });
  }
});
</script>

<template>
  <div class="min-h-screen mb-40">


    <section v-if="!isLoading"
      class="-mt-30 md:-mt-12 min-h-screen flex justify-center py-24 px-4 sm:px-6 font-sans text-black selection:bg-black selection:text-white">
      <div class="container max-w-[650px] w-full flex flex-col space-y-12 mt-10 mx-auto">

        <!-- About Section -->
        <div class="anim-box flex flex-col space-y-4">
          <h1 class="anim-text text-2xl md:text-3xl font-bold tracking-wide text-black">
            Gilang Abdian
          </h1>

          <div class="anim-text space-y-6 text-sm md:text-base text-gray-700 font-normal leading-relaxed">
            <p>
              Hi, I am Gilang Abdian Anggara. I am a Frontend Developer dedicated to creating digital experiences that
              are simple, fast, and accessible. I focus on building interfaces that feel natural and work perfectly
              across all devices.
            </p>
            <p>
              I believe that good frontend development is timeless. It is about more than just looks—it is about writing
              clean, maintainable code and putting the user first. My goal is to turn complex ideas into smooth,
              high-performance web applications that stay relevant as technology evolves.
            </p>


            <!-- What I Do Section -->
            <!-- <div class="anim-box space-y-4">
              <p class="anim-text text-sm md:text-base text-black font-normal">
                What I do:
              </p>

              <ul
                class="anim-text list-disc list-outside pl-5 space-y-2 text-sm md:text-base text-gray-700 font-normal leading-relaxed">
                <li><strong>Crafting Modern UIs</strong>: Building responsive and beautiful interfaces using the latest web technologies.</li>
                <li><strong>Performance & Accessibility</strong>: Ensuring websites are fast, lightweight, and accessible to everyone.</li>
                <li><strong>Clean & Scalable Code</strong>: Writing modular and well-organized code that is easy to maintain and grow.</li>
                <li><strong>Seamless Interaction</strong>: Creating smooth animations and micro-interactions to enhance the user experience.</li>
              </ul>
            </div> -->

            <p>
              Outside of programming, I enjoy making YouTube videos, playing the guitar, and finding new ways to blend
              technology with creativity to stay inspired.
            </p>
          </div>
        </div>


      </div>
    </section>
  </div>
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
</style>