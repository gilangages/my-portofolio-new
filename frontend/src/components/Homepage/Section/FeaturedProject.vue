<script setup>
import { onMounted, ref } from "vue";
import { getAllProjects } from "../../lib/api/ProjectApi";

const projects = ref([]);
async function fetchProjects() {
  const response = await getAllProjects();
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.status === 200) {
    projects.value = responseBody;
  } else {
    await alertError(responseBody.message);
  }
}

onMounted(async () => {
  await fetchProjects();
});
</script>
<template>
  <h1 class="text-center">featured projects</h1>
  <div v-for="project in projects" :id="project.id">
    <img :src="project.thumbnail_url" alt="" />
  </div>
</template>
