<script setup>
import { onMounted, ref } from "vue";
import { Icon } from "@iconify/vue";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

const ctaRef = ref(null);

onMounted(() => {
  // Animasi CTA (Call to Action)
  gsap.fromTo(
    ".cta-animate", // Target class yang kita tambahkan di template
    {
      y: 50,
      opacity: 0,
      scale: 0.9,
    },
    {
      scrollTrigger: {
        trigger: ctaRef.value,
        start: "top 50%", // Mulai saat box masuk 80% layar
        once: true,
      },
      y: 0,
      opacity: 1,
      scale: 1,
      duration: 0.8,
      stagger: 0.2, // Jeda antar elemen (Judul -> Teks -> Tombol)
      ease: "elastic.out(1, 0.75)", // Efek membal biar tombolnya terasa "greget"
    },
  );
});
</script>

<template>
  <section>
    <div ref="ctaRef" class="bg-white border-black border-y-4 py-20 px-4 md:px-10 overflow-hidden">
      <div class="max-w-5xl mx-auto text-center">
        <h2 class="cta-animate text-4xl md:text-6xl font-black font-serif uppercase mb-6 leading-tight">
          Have an Idea?
          <br />
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-black to-gray-600">
            Let's Build It Together!
          </span>
        </h2>

        <p
          class="cta-animate mb-10 font-[Inter] text-gray-500 text-sm md:text-base lowercase tracking-tight max-w-xl mx-auto">
          open to discussing new ventures. ready to translate your concepts into high-quality digital solutions.
        </p>

        <div class="cta-animate inline-block">
          <a
            href="mailto:your.email@example.com"
            class="inline-flex items-center gap-2 px-8 py-4 bg-white border-2 rounded-2xl border-black text-black font-black uppercase tracking-wider text-lg shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[3px] hover:translate-y-[3px] transition-all">
            <Icon icon="mdi:email-outline" class="text-2xl" />
            Contact Me
          </a>
        </div>
      </div>
    </div>
  </section>
</template>
