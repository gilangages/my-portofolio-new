<script setup>
import { Icon } from "@iconify/vue";
import { onMounted, ref } from "vue";
import { getAllContacts } from "../../lib/api/ContactApi"; // Menggunakan ContactApi

const currentYear = new Date().getFullYear();

// Variable untuk menampung data kontak dari API
const contacts = ref([]);
const loading = ref(true);

// Data Menu (Tetap statis karena ini navigasi halaman)
const menus = [
  { name: "Home", href: "#hero" },
  { name: "Tech Stack", href: "#tech" },
  { name: "Projects", href: "#projects" },
  { name: "Certificates", href: "#certificates" },
  { name: "Journey", href: "#experience" },
];

// Fetch Data Contact (Social Media)
async function fetchContacts() {
  loading.value = true;
  try {
    const response = await getAllContacts();
    const responseBody = await response.json();

    if (response.status === 200) {
      contacts.value = responseBody;
    } else {
      console.error(responseBody.message);
    }
  } catch (error) {
    console.error("Failed to fetch contacts");
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  await fetchContacts();
});

function scrollToSection(href) {
  const element = document.querySelector(href);
  if (element) {
    element.scrollIntoView({ behavior: "smooth" });
  }
}
</script>

<template>
  <footer class="relative">
    <div class="bg-white pt-16 pb-8 px-4 md:px-10">
      <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 mb-16">
        <div class="flex flex-col items-center md:items-start text-center md:text-left">
          <h3 class="text-3xl font-black font-serif uppercase tracking-widest border-b-4 border-black pb-1 mb-4">
            ABDIAN
          </h3>
          <p class="text-gray-600 font-medium leading-relaxed">
            Passionate about digital arts, I craft web platforms that balance function and aesthetics. Always learning
            and experimenting, I am committed to delivering the best results in every project.
          </p>
        </div>

        <div class="flex flex-col items-center md:items-start">
          <h4 class="font-bold text-lg uppercase mb-6 flex items-center gap-2">
            <Icon icon="mdi:compass-outline" class="text-xl" />
            Explore
          </h4>
          <ul class="space-y-3 text-center md:text-left">
            <li v-for="menu in menus" :key="menu.name">
              <a
                :href="menu.href"
                @click.prevent="scrollToSection(menu.href)"
                class="text-gray-600 font-bold hover:text-black hover:underline decoration-2 underline-offset-4 transition-colors">
                {{ menu.name }}
              </a>
            </li>
          </ul>
        </div>

        <div class="flex flex-col items-center md:items-start">
          <h4 class="font-bold text-lg uppercase mb-6 flex items-center gap-2">
            <Icon icon="mdi:share-variant-outline" class="text-xl" />
            Connect
          </h4>

          <div v-if="loading" class="text-sm text-gray-400 font-bold">Loading...</div>

          <div v-else class="flex flex-wrap justify-center md:justify-start gap-4">
            <a
              v-for="contact in contacts"
              :key="contact.id"
              :href="contact.url"
              target="_blank"
              class="p-3 border-2 border-black rounded-lg hover:bg-black hover:text-white transition-all hover:-translate-y-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] group"
              :aria-label="contact.name || 'Social Link'">
              <Icon :icon="contact.icon" class="text-2xl transition-transform group-hover:scale-110" />
            </a>
          </div>
        </div>
      </div>

      <div class="border-t-2 border-dashed border-gray-300 pt-8 text-center">
        <p class="text-sm font-bold text-gray-500 uppercase tracking-wide">
          &copy; {{ currentYear }} Abdian. All rights reserved.
          <span class="hidden md:inline mx-2">•</span>
          <br class="md:hidden" />
          Designed with love for you
        </p>
      </div>
    </div>
  </footer>
</template>
