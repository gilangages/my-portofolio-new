<script setup>
import { onMounted, ref } from "vue";
import Navbar from "../Navbar.vue";
import { alertError } from "../lib/alert";
import { getAllCertificates } from "../lib/api/CertificateApi";

const certificates = ref([]);
const loading = ref(true);
async function fetchCertificates() {
  loading.value = true;
  try {
    const response = await getAllCertificates();
    const responseBody = await response.json();
    console.log(responseBody);

    if (response.status === 200) {
      certificates.value = responseBody.data || responseBody;
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
  await fetchCertificates();
});
</script>
<template>
  <div class="px-4 py-4 align-content-center justify-content-center">
    <Navbar />
    <p class="text-center text-black text-2xl bg-gray-100">this is all certificates</p>
    <div v-if="loading">wait a minute</div>

    <div v-else="certificates" class="grid grid-cols-1 md:grid-cols-3 gap-2">
      <div v-for="certificate in certificates" :key="certificate.id">
        <div>{{ certificate.title }}</div>
      </div>
    </div>
  </div>
</template>
