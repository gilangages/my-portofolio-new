<script setup>
import { onMounted } from "vue";
import { initConsoleFeatures } from "./utils/consoleManager";
import { initSmoothScroll } from "./lib/smoothScroll";

onMounted(() => {
  initConsoleFeatures();
  initSmoothScroll();
});
</script>

<template>
  <RouterView />
</template>

<style scoped></style>
