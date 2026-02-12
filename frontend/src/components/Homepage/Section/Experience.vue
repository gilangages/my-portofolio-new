<script setup>
import { onMounted, ref, computed } from "vue";
import { getAllExperiences } from "../../lib/api/ExperienceApi"; // Sesuaikan path ini jika perlu
import { Icon } from "@iconify/vue";

const experiences = ref([]);
const loading = ref(true);

// Fetch Data
async function fetchExperiences() {
  loading.value = true;
  try {
    const response = await getAllExperiences();
    const responseBody = await response.json();

    // Handle jika responseBody dibungkus { data: [...] } atau langsung array [...]
    const data = responseBody.data || responseBody;

    if (response.ok) {
      experiences.value = Array.isArray(data) ? data : [];
    } else {
      console.error("Error fetching data");
    }
  } catch (error) {
    console.error("Failed to fetch experiences", error);
  } finally {
    loading.value = false;
  }
}

// Logic Sort: Terbaru di atas
const sortedExperiences = computed(() => {
  return [...experiences.value].sort((a, b) => {
    return new Date(b.start_date) - new Date(a.start_date);
  });
});

// Helper Format Tanggal
function formatDate(dateString) {
  if (!dateString) return "PRESENT";
  const date = new Date(dateString);
  return new Intl.DateTimeFormat("en-US", { month: "short", year: "numeric" }).format(date).toUpperCase();
}

// Helper Durasi (Opsional, agar terlihat lebih pro)
function getDuration(start, end) {
  const startDate = new Date(start);
  const endDate = end ? new Date(end) : new Date();
  const diffTime = Math.abs(endDate - startDate);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  const years = Math.floor(diffDays / 365);
  const months = Math.floor((diffDays % 365) / 30);

  if (years > 0) return `${years} Yrs ${months} Mos`;
  return `${months} Mos`;
}

onMounted(async () => {
  await fetchExperiences();
});
</script>

<template>
  <section class="py-20 bg-white">
    <div class="max-w-5xl mx-auto px-4 md:px-6">
      <div class="text-center mb-12">
        <h2
          class="text-4xl font-black text-black mb-6 font-serif uppercase tracking-wider inline-block relative border-b-4 border-black pb-2">
          <span class="relative z-10">My Journey</span>
          <span class="absolute top-0 left-0 w-full h-full bg-[#E7E7E7] -z-0 -rotate-2 opacity-50"></span>
        </h2>
        <p class="mt-4 font-mono text-gray-500 text-sm md:text-base lowercase tracking-tight max-w-xl mx-auto">
          just building things, breaking things, and learning from the pieces. slowly but surely.
        </p>
      </div>

      <div v-if="loading" class="flex flex-col items-center justify-center py-12 gap-4">
        <Icon icon="svg-spinners:blocks-scale" class="text-4xl" />
        <span class="font-mono font-bold animate-pulse">LOADING HISTORY...</span>
      </div>

      <div v-else-if="experiences.length === 0" class="text-center py-12 border-4 border-dashed border-gray-300">
        <p class="font-mono text-gray-400">No experience data found.</p>
      </div>

      <div v-else class="relative">
        <div class="absolute left-4 md:left-1/2 top-0 bottom-0 w-1 md:-ml-0.5 bg-black z-0"></div>

        <div class="relative z-10 mb-12 flex items-center md:justify-center pl-4 md:pl-0">
          <div
            class="bg-black text-white font-black px-4 py-1 uppercase text-sm border-2 border-black shadow-[4px_4px_0px_0px_rgba(255,255,255,1),6px_6px_0px_0px_rgba(0,0,0,1)] animate-bounce">
            NOW / FUTURE
          </div>
        </div>

        <div class="space-y-12 md:space-y-0">
          <div
            v-for="(exp, index) in sortedExperiences"
            :key="exp.id"
            class="relative flex flex-col md:flex-row items-start group"
            :class="index % 2 === 0 ? 'md:flex-row-reverse' : ''">
            <div
              class="absolute left-4 md:left-1/2 w-4 h-4 -ml-[6px] md:-ml-2 bg-white border-4 border-black rounded-full z-20 top-8 group-hover:scale-150 group-hover:bg-[#E7E7E7] transition-transform duration-300"></div>

            <div class="hidden md:block w-1/2 px-10 pt-6" :class="index % 2 === 0 ? 'text-left' : 'text-right'">
              <div class="font-black text-2xl uppercase italic">{{ formatDate(exp.start_date) }}</div>
              <div class="font-mono text-gray-500 font-bold text-sm">
                {{ exp.end_date ? formatDate(exp.end_date) : "PRESENT" }}
              </div>
              <div class="mt-2 inline-block bg-[#E7E7E7] border-2 border-black px-2 py-0.5 text-xs font-bold font-mono">
                {{ getDuration(exp.start_date, exp.end_date) }}
              </div>
            </div>

            <div class="w-full md:w-1/2 pl-12 md:pl-0 pr-0 md:px-10">
              <div
                class="relative bg-white border-4 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] group-hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] group-hover:-translate-y-1 transition-all duration-300">
                <div class="md:hidden mb-4 border-b-2 border-dashed border-gray-300 pb-2">
                  <span class="bg-black text-white text-xs font-bold px-2 py-1 uppercase">
                    {{ formatDate(exp.start_date) }} — {{ exp.end_date ? formatDate(exp.end_date) : "NOW" }}
                  </span>
                </div>

                <h3 class="text-xl md:text-2xl font-black uppercase leading-tight mb-1">
                  {{ exp.role }}
                </h3>

                <div class="flex flex-wrap items-center gap-2 mb-4">
                  <span class="text-blue-700 font-bold flex items-center gap-1">
                    <Icon icon="lucide:building-2" />
                    {{ exp.company_name }}
                  </span>
                  <span class="text-xs font-mono border border-black px-2 py-0.5 bg-gray-100 uppercase">
                    {{ exp.status }}
                  </span>
                </div>

                <div v-if="exp.location" class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase mb-4">
                  <Icon icon="lucide:map-pin" class="text-red-500" />
                  {{ exp.location }}
                </div>

                <p class="font-mono text-sm leading-relaxed text-gray-700 whitespace-pre-line">
                  {{ exp.description }}
                </p>

                <div v-if="!exp.end_date" class="absolute -top-3 -right-3 rotate-3">
                  <span class="bg-[#E7E7E7] border-2 border-black px-3 py-1 text-xs font-black uppercase shadow-sm">
                    Current
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="relative z-10 mt-12 flex items-center md:justify-center pl-4 md:pl-0">
          <div
            class="bg-white text-black font-black px-4 py-2 uppercase text-sm border-4 border-black flex items-center gap-2 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
            <Icon icon="lucide:flag" />
            START JOURNEY
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
