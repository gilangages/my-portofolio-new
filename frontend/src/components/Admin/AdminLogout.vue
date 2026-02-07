<script setup>
import { adminLogout } from "../llib/api/adminApi";
import { useLocalStorage } from "@vueuse/core";
import { useRouter } from "vue-router";
import { alertError } from "../llib/alert";
import { onBeforeMount } from "vue";

const token = useLocalStorage("token", "");

const router = useRouter();

async function handleLogout() {
  const response = await adminLogout(token);
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.status === 200) {
    token.value = "";
    await router.push({
      path: "/admin/login",
    });
  } else {
    await alertError(responseBody.errors);
  }
}

onBeforeMount(async () => {
  await handleLogout();
});
</script>
<template></template>

<style scoped></style>
