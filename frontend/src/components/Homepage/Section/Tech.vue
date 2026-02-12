<script setup>
import { onMounted, ref } from "vue";
import { alertError } from "../../lib/alert";
import { getSkills } from "../../lib/api/SkillApi";

const skills = ref(null);

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
  <div v-if="skills">
    <h1>techstack</h1>
    <div class="text-blue-900">
      {{ skills.name }}
      {{ skills.category }}
    </div>
  </div>
</template>
