<script setup>
import { onMounted, ref, computed } from "vue";
import { getAllExperiences } from "../../lib/api/ExperienceApi";
import { Icon } from "@iconify/vue";

const experiences = ref([]);
const loading = ref(true);

async function fetchExperiences() {
  loading.value = true;
  try {
    const response = await getAllExperiences();
    const responseBody = await response.json();

    if (response.status === 200) {
      experiences.value = responseBody;
    } else {
      console.error(responseBody.message);
    }
  } catch (error) {
    console.error("Failed to fetch experiences");
  } finally {
    loading.value = false;
  }
}

// Logic: Mengurutkan pengalaman dari yang terbaru (Start Date paling besar) ke terlama
const sortedExperiences = computed(() => {
  return [...experiences.value].sort((a, b) => {
    return new Date(b.start_date) - new Date(a.start_date);
  });
});

// Helper: Format Tanggal (misal: 2026-02-01 -> Feb 2026)
function formatDate(dateString) {
  if (!dateString) return "Present";
  const date = new Date(dateString);
  return new Intl.DateTimeFormat("en-US", { month: "short", year: "numeric" }).format(date);
}

onMounted(async () => {
  await fetchExperiences();
});
</script>

<template>
  <section v-if="!loading && experiences.length > 0" class="py-20 px-4 md:px-10 bg-white">
    <div class="max-w-4xl mx-auto">
      <div class="text-center mb-16">
        <h2
          class="text-4xl font-black text-black mb-6 font-serif uppercase tracking-wider inline-block relative border-b-4 border-black pb-2">
          <span class="relative z-10">My Journey</span>
          <span class="absolute top-0 left-0 w-full h-full bg-[#E7E7E7] -z-0 -rotate-2 opacity-50"></span>
        </h2>
      </div>

      <div class="relative border-l-4 border-black ml-4 md:ml-10 space-y-12 pl-8 md:pl-12">
        <div v-for="(exp, index) in sortedExperiences" :key="exp.id" class="relative">
          <div
            class="absolute -left-[46px] md:-left-[62px] top-6 w-6 h-6 bg-[#cac9c9] border-4 border-black rounded-full z-10"></div>

          <div
            class="bg-white border-2 border-black p-6 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[4px] hover:translate-y-[4px] transition-all duration-200">
            <div class="absolute -top-4 -right-2 md:right-4">
              <span
                class="bg-black text-white text-xs font-bold px-3 py-1.5 rounded-sm uppercase tracking-wider shadow-[2px_2px_0px_0px_#cac9c9] transform rotate-1">
                {{ formatDate(exp.start_date) }} - {{ formatDate(exp.end_date) }}
              </span>
            </div>

            <div class="mb-4 pr-16 md:pr-0">
              <h3 class="text-2xl font-black font-serif uppercase leading-tight mb-1">
                {{ exp.role }}
              </h3>
              <div class="flex flex-wrap items-center gap-2 text-sm font-bold text-gray-700">
                <span class="flex items-center gap-1">
                  <Icon icon="mdi:office-building" />
                  {{ exp.company_name }}
                </span>
                <span v-if="exp.status" class="px-2 py-0.5 border border-black bg-gray-100 text-xs rounded-sm">
                  {{ exp.status }}
                </span>
              </div>
            </div>

            <div
              v-if="exp.location"
              class="flex items-center gap-1 text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">
              <Icon icon="mdi:map-marker" />
              {{ exp.location }}
            </div>

            <div
              class="text-gray-800 text-sm md:text-base font-medium leading-relaxed whitespace-pre-line border-t-2 border-dashed border-gray-300 pt-4">
              {{ exp.description }}
            </div>
          </div>
        </div>

        <div class="absolute -left-[41px] md:-left-[57px] bottom-0 w-4 h-4 bg-black rounded-full"></div>
      </div>
    </div>
  </section>
</template>
