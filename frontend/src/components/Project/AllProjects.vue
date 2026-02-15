<script setup>
import { onMounted, ref } from "vue";
import { getAllProjects } from "../lib/api/ProjectApi";
import Navbar from "../Navbar.vue";
import { alertError } from "../lib/alert";

const projects = ref([]);
const loading = ref(true);
async function fetchProjects() {
  loading.value = true;
  try {
    const response = await getAllProjects();
    const responseBody = await response.json();
    console.log(responseBody);

    if (response.status === 200) {
      projects.value = responseBody.data || responseBody;
    } else {
      await alertError(responseBody.message);
    }
  } catch (e) {
    console.error(`Error fetch projects:`, e);
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  await fetchProjects();
});
</script>
<template>
  <div class="px-4 py-4 flex align-content-center justify-content-center">
    <Navbar />
    this is all projects
    <div v-if="loading">wait a minute</div>

    <div v-else="projects" class="grid grid-cols-1 md:grid-cols-3 gap-2">
      <div v-for="project in projects" :key="project.id">
        <div>{{ project.title }}</div>
      </div>
    </div>
  </div>
</template>
