<script setup>
import Hero from "./Section/Hero.vue";
import Navbar from "./Section/Navbar.vue";
import Tech from "./Section/Tech.vue";
</script>

<template>
  <div
    class="min-h-screen bg-white text-black font-sans selection:bg-black selection:text-white overflow-x-hidden flex flex-col pb-20 md:pb-0">
    <Navbar />
    <Hero />
    <Tech />
  </div>
</template>

<style scoped></style>
