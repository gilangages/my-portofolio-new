<script setup>
import { onMounted, ref } from "vue";
import { getProfile } from "../lib/api/ProfileApi";
import { alertError } from "../lib/alert";

const profile = ref(null);
const isLoading = ref(true);

async function fetchProfile() {
  isLoading.value = true;
  try {
    const response = await getProfile();
    const responseBody = await response.json();
    console.log(responseBody);

    if (response.status === 200) {
      profile.value = responseBody || responseBody.data;
    } else {
      await alertError(responseBody.message);
    }
  } catch (e) {
    console.error(`error fetch about: ${e}`);
  } finally {
    isLoading.value = false;
  }
}

onMounted(async () => {
  await fetchProfile();
});
</script>
<template>
  <div v-if="isLoading">wat a minute</div>
  <div v-if="profile">{{ profile.about.name }}</div>
</template>
