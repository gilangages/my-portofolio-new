<script setup>
import { Icon } from "@iconify/vue";
import { onMounted, ref } from "vue";
import { getAllContacts } from "../../lib/api/ContactApi";

const currentYear = new Date().getFullYear();

const contacts = ref([]);
const loading = ref(true);

// Menu Navigasi (Tetap lowercase)
const menus = [
  { name: "home", href: "#hero" },
  { name: "tech stack", href: "#tech" },
  { name: "projects", href: "#projects" },
  { name: "certificates", href: "#certificates" },
  { name: "journey", href: "#experience" },
];

async function fetchContacts() {
  loading.value = true;
  try {
    const response = await getAllContacts();
    const responseBody = await response.json();

    if (response.status === 200) {
      contacts.value = responseBody.data || responseBody;
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
  <footer class="relative font-[Inter] text-black">
    <div class="bg-white pt-16 pb-8 px-4 md:px-10 border-t-4 border-black">
      <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 mb-16">
        <div class="flex flex-col items-center md:items-start text-center md:text-left">
          <h3 class="text-3xl font-black font-serif uppercase tracking-widest border-b-4 border-black pb-1 mb-4">
            ABDIAN
          </h3>
          <p class="text-gray-700 text-sm leading-relaxed lowercase">
            digital artisan. balancing aesthetic integrity with functional architecture. committed to continuous
            deployment of high-quality web solutions.
          </p>
        </div>

        <div class="flex flex-col items-center md:items-start">
          <h4 class="font-bold text-sm uppercase mb-6 flex items-center gap-2 border-b-2 border-black pb-1">
            <Icon icon="mdi:console-line" class="text-lg" />
            System Navigation
          </h4>
          <ul class="space-y-2 text-center md:text-left">
            <li v-for="menu in menus" :key="menu.name">
              <a
                :href="menu.href"
                @click.prevent="scrollToSection(menu.href)"
                class="group text-gray-500 font-bold text-sm hover:text-black transition-all lowercase inline-flex items-center gap-1 justify-center md:justify-start whitespace-nowrap">
                <Icon
                  icon="mdi:console-line"
                  class="opacity-0 group-hover:opacity-100 -ml-4 group-hover:ml-0 transition-all duration-300 text-black text-xs" />

                {{ menu.name }}
              </a>
            </li>
          </ul>
        </div>

        <div class="flex flex-col items-center md:items-start">
          <h4 class="font-bold text-sm uppercase mb-6 flex items-center gap-2 border-b-2 border-black pb-1">
            <Icon icon="mdi:network-outline" class="text-lg" />
            Network Nodes
          </h4>

          <div v-if="loading" class="text-xs text-gray-400 font-bold animate-pulse lowercase">
            establishing connection...
          </div>

          <div v-else class="flex flex-wrap justify-center md:justify-start gap-3">
            <a
              v-for="contact in contacts"
              :key="contact.id"
              :href="contact.url"
              target="_blank"
              class="p-2 border-2 border-black bg-white text-black hover:bg-black hover:text-white transition-all hover:-translate-y-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] group"
              :aria-label="contact.name || 'Social Link'">
              <Icon :icon="contact.icon" class="text-xl" />
            </a>
          </div>
        </div>
      </div>

      <div class="border-t-2 border-dashed border-gray-300 pt-8 text-center">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">
          &copy; {{ currentYear }} Abdian. All systems operational.
          <span class="hidden md:inline mx-2">//</span>
          <br class="md:hidden" />
          <span class="lowercase">engineered for performance & aesthetics</span>
        </p>
      </div>
    </div>
  </footer>
</template>
