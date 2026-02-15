<script setup>
import { onMounted, ref } from "vue";
import Navbar from "../Navbar.vue";
import { alertError } from "../lib/alert";
import { getAllContacts } from "../lib/api/ContactApi";
import { Icon } from "@iconify/vue";

const contacts = ref([]);
const loading = ref(true);
async function fetchContacts() {
  loading.value = true;
  try {
    const response = await getAllContacts();
    const responseBody = await response.json();
    console.log(responseBody);

    if (response.status === 200) {
      contacts.value = responseBody.data || responseBody;
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
  await fetchContacts();
});
</script>
<template>
  <div class="px-4 py-4 flex align-content-center justify-content-center">
    <Navbar />
    this is all projects
    <div v-if="loading">wait a minute</div>

    <div v-else="projects" class="grid grid-cols-1 md:grid-cols-3 gap-2">
      <div v-for="contact in contacts" :key="contact.id">
        <div>{{ contact.platform_name }}</div>
        <Icon :icon="contact.icon" />
      </div>
    </div>
  </div>
</template>
