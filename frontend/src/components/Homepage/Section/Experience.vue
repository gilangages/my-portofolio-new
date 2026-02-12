<script setup>
import { onMounted, ref } from "vue";
import { getAllExperiences } from "../../lib/api/ExperienceApi";
import { alertError } from "../../lib/alert";

const experiences = ref([]);
async function fetchExperiences() {
  const response = await getAllExperiences();
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.status === 200) {
    experiences.value = responseBody;
  } else {
    await alertError(responseBody.message);
  }
}

onMounted(async () => {
  await fetchExperiences();
});
</script>
<template>
  <h1>my journey</h1>
  <div v-for="experience in experiences" :id="experience.id">
    <div>{{ experience.company_name }}</div>
    <div>{{ experience.role }}</div>
  </div>
</template>
