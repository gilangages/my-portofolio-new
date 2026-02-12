<script setup>
import { onMounted, ref } from "vue";
import { getAllCertificates } from "../../lib/api/CertificateApi";
import { alertError } from "../../lib/alert";

const certificates = ref([]);
async function fetchCertificates() {
  const response = await getAllCertificates();
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.status === 200) {
    certificates.value = responseBody;
  } else {
    await alertError(responseBody.message);
  }
}

onMounted(async () => {
  await fetchCertificates();
});
</script>
<template>
  <section id="certificates" class="py-20 px-4 md:px-10">
    <div class="max-w-6xl mx-auto">
      <h2 class="text-3xl font-bold text-center mb-12 font-serif">Featured Certificates</h2>

      <div v-if="certificates.length === 0" class="text-center text-gray-400">Loading certificates...</div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="cert in certificates"
          :key="cert.id"
          class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 group">
          <div class="h-40 bg-gray-100 rounded-lg mb-4 flex items-center justify-center overflow-hidden">
            <img :src="cert.image_url" alt="" />
          </div>

          <h3 class="text-xl font-bold font-serif mb-2">{{ cert.name }}</h3>
          <p class="text-sm text-gray-500 mb-4">{{ cert.issuer }} • {{ cert.issued_at }}</p>

          <a
            :href="cert.credential_link"
            target="_blank"
            class="inline-flex items-center text-sm font-bold border-b-2 border-black pb-0.5 hover:text-gray-600 hover:border-gray-600 transition-colors">
            View Credential
            <Icon icon="mdi:arrow-top-right" class="ml-1" />
          </a>
        </div>
      </div>
    </div>
  </section>
</template>
