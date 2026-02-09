<script setup>
import { reactive } from "vue";
import { adminLogin } from "../lib/api/AdminApi";
import { useLocalStorage } from "@vueuse/core";
import { useRouter } from "vue-router";
import { alertError } from "../lib/alert";
import { Icon } from "@iconify/vue";

const token = useLocalStorage("token", "");
const admin = reactive({
  email: "",
  password: "",
});
const router = useRouter();

async function handleSubmit() {
  const response = await adminLogin(admin);
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.status === 200) {
    token.value = responseBody.token;
    await router.push({
      path: "/admin/dashboard",
    });
  } else {
    await alertError(responseBody.errors);
  }
}
</script>
<template>
  <div
    class="min-h-screen bg-gray-100 flex items-center justify-center p-6 font-sans selection:bg-black selection:text-white">
    <div
      class="absolute inset-0 opacity-10 pointer-events-none"
      style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 20px 20px"></div>

    <div
      class="w-full max-w-md bg-white border-2 border-black rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] relative z-10 overflow-hidden">
      <div class="bg-black text-white p-6 text-center border-b-2 border-black">
        <div
          class="inline-block bg-white text-black border-2 border-black rounded-full p-3 mb-3 shadow-[3px_3px_0px_0px_rgba(255,255,255,0.5)]">
          <Icon icon="mdi:shield-account-outline" class="w-8 h-8" />
        </div>
        <h1 class="text-2xl font-bold font-serif tracking-wide">ADMIN PORTAL</h1>
        <p class="text-xs uppercase tracking-widest mt-1 text-gray-300">Restricted Area</p>
      </div>

      <form v-on:submit.prevent="handleSubmit">
        <div class="p-8 space-y-6">
          <div class="space-y-2 group">
            <label class="block text-sm font-bold uppercase tracking-wider">Email Address</label>
            <div class="relative flex items-center">
              <Icon
                icon="mdi:email-outline"
                class="absolute left-3 w-5 h-5 text-gray-500 group-focus-within:text-black transition-colors" />
              <input
                v-model="admin.email"
                type="email"
                placeholder="admin@example.com"
                class="w-full bg-gray-50 border-2 border-black rounded-lg py-3 pl-10 pr-4 outline-none transition-all placeholder:text-gray-400 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:-translate-y-0.5" />
            </div>
          </div>

          <div class="space-y-2 group">
            <label class="block text-sm font-bold uppercase tracking-wider">Password</label>
            <div class="relative flex items-center">
              <Icon
                icon="mdi:lock-outline"
                class="absolute left-3 w-5 h-5 text-gray-500 group-focus-within:text-black transition-colors" />
              <input
                v-model="admin.password"
                type="password"
                placeholder="••••••••"
                class="w-full bg-gray-50 border-2 border-black rounded-lg py-3 pl-10 pr-4 outline-none transition-all placeholder:text-gray-400 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:-translate-y-0.5" />
            </div>
          </div>

          <button
            class="w-full bg-black text-white font-bold py-3.5 rounded-lg border-2 border-black text-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:bg-white hover:text-black hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[4px] active:translate-y-[4px] transition-all flex items-center justify-center gap-2 mt-4">
            <span>Enter Dashboard</span>
            <Icon icon="mdi:arrow-right" class="w-5 h-5" />
          </button>
        </div>
      </form>

      <div class="bg-gray-100 p-3 text-center border-t-2 border-black text-[10px] font-mono">
        SYSTEM_STATUS:
        <span class="text-green-600 font-bold">ONLINE</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-serif {
  font-family: "DM Serif Display", serif;
}
</style>
