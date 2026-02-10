<script setup>
import Hero from "./Section/Hero.vue";
import Navbar from "./Section/Navbar.vue";
</script>

<template>
  <div
    class="min-h-screen bg-white text-black font-sans selection:bg-black selection:text-white overflow-x-hidden flex flex-col pb-32 md:pb-0">
    <Navbar />
    <Hero />
  </div>
</template>

<style scoped></style>
