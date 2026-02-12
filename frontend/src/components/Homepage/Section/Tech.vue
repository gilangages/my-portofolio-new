<script setup>
import { onMounted, ref } from "vue";
import { alertError } from "../../lib/alert";
import { getSkills } from "../../lib/api/SkillApi";
import { Icon } from "@iconify/vue";

const skills = ref([]);

async function fetchSkills() {
  const response = await getSkills();
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.status === 200) {
    skills.value = responseBody;
  } else {
    await alertError(responseBody.message);
  }
}

onMounted(async () => {
  await fetchSkills();
});
</script>
<template>
  <section class="py-20 px-4 md:px-10">
    <div class="max-w-4xl mx-auto">
      <h2 class="text-3xl font-bold text-center mb-10 font-serif">Tech Stack</h2>

      <div class="grid grid-cols-3 md:grid-cols-5 gap-6">
        <div
          v-for="skill in skills"
          :key="skill.id"
          class="flex flex-col items-center justify-center p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-all hover:-translate-y-1 border border-gray-100">
          <Icon :icon="skill.identifier" class="w-10 h-10 mb-2 text-gray-700" />
          <span class="text-sm font-medium text-gray-600">{{ skill.name }}</span>
        </div>
      </div>
    </div>
  </section>
</template>
