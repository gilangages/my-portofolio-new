<script setup>
import Experience from "./Section/Experience.vue";
import FeaturedCertificate from "./Section/FeaturedCertificate.vue";
import FeaturedProject from "./Section/FeaturedProject.vue";
import Footer from "./Section/Footer.vue";
import Hero from "./Section/Hero.vue";
import Navbar from "./Section/Navbar.vue";
import Tech from "./Section/Tech.vue";
</script>

<template>
  <div
    class="min-h-screen bg-white text-black font-sans selection:bg-black selection:text-white overflow-x-hidden flex flex-col pb-24 md:pb-0">
    <Navbar />
    <Hero />
    <Tech />
    <FeaturedProject />
    <FeaturedCertificate />
    <Experience />
    <Footer />
  </div>
</template>

<style scoped></style>
