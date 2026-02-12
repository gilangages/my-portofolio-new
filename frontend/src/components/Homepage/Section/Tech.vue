<script setup>
import { Icon } from "@iconify/vue";

const props = defineProps({
  skills: {
    type: Array,
    required: true,
    default: () => [], // Fallback agar tidak error jika null
  },
});
</script>

<template>
  <section v-if="props.skills && props.skills.length > 0" class="py-20 px-4 md:px-10 bg-white">
    <div class="max-w-4xl mx-auto">
      <div class="text-center mb-12">
        <h2
          class="text-4xl font-black text-black mb-6 font-serif uppercase tracking-wider inline-block relative border-b-4 border-black pb-2">
          <span class="relative z-10">Tech Stack</span>
          <span class="absolute top-0 left-0 w-full h-full bg-[#E7E7E7] -z-0 -rotate-2 opacity-50"></span>
        </h2>

        <p class="mt-4 font-[Inter] text-gray-500 text-sm md:text-base lowercase tracking-tight max-w-xl mx-auto">
          here are the tools and technologies I have worked with to build my projects. My stack evolves as I learn, but
          every tool has played a role in my journey. I am mostly self-taught and still learning every day—treating
          every line of code as an opportunity to discover something new.
        </p>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
        <div
          v-for="skill in props.skills"
          :key="skill.id"
          class="group flex flex-col items-center justify-center p-6 bg-white border-2 border-black rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all duration-200 cursor-default">
          <div class="p-2 mb-3 bg-gray-50 border border-black rounded-md group-hover:bg-[#E7E7E7] transition-colors">
            <Icon :icon="skill.identifier" class="w-10 h-10 text-black" />
          </div>
          <span class="text-sm font-bold text-black uppercase tracking-wide">{{ skill.name }}</span>
        </div>
      </div>
    </div>
  </section>
</template>
