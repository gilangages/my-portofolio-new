<script setup>
import { ref, onMounted } from "vue";
import { getAllProjects } from "../../lib/api/ProjectApi";
import { getAllCertificates } from "../../lib/api/CertificateApi";
import { alertError } from "../../lib/alert";
import { getSkills } from "../../lib/api/SkillApi";
import { getAllContacts } from "../../lib/api/ContactApi";
// import AdminApi from "../../llib/api/AdminApi"; // Sesuaikan path

const projects = ref(0);
const certificates = ref(0);
const skills = ref(0);
const contacts = ref(0);
const isLoading = ref(true);

async function fetchProjects() {
  isLoading.value = false;
  try {
    const response = await getAllProjects();
    const responseBody = await response.json();
    console.log(responseBody);

    const data = responseBody.data || responseBody;

    if (response.status === 200) {
      projects.value = Array.isArray(data) ? data.length : 0;
    } else {
      await alertError(responseBody.message);
    }
  } catch (e) {
    console.error(e);
  } finally {
    isLoading.value = false;
  }
}

async function fetchCertificates() {
  isLoading.value = false;
  try {
    const response = await getAllCertificates();
    const responseBody = await response.json();
    console.log(responseBody);

    const data = responseBody.data || responseBody;

    if (response.status === 200) {
      certificates.value = Array.isArray(data) ? data.length : 0;
    } else {
      await alertError(responseBody.message);
    }
  } catch (e) {
    console.error(e);
  } finally {
    isLoading.value = false;
  }
}

async function fetchSkills() {
  isLoading.value = false;
  try {
    const response = await getSkills();
    const responseBody = await response.json();
    console.log(responseBody);

    const data = responseBody.data || responseBody;

    if (response.status === 200) {
      skills.value = Array.isArray(data) ? data.length : 0;
    } else {
      await alertError(responseBody.message);
    }
  } catch (e) {
    console.error(e);
  } finally {
    isLoading.value = false;
  }
}
async function fetchContacts() {
  isLoading.value = false;
  try {
    const response = await getAllContacts();
    const responseBody = await response.json();
    console.log(responseBody);

    const data = responseBody.data || responseBody;

    if (response.status === 200) {
      contacts.value = Array.isArray(data) ? data.length : 0;
    } else {
      await alertError(responseBody.message);
    }
  } catch (e) {
    console.error(e);
  } finally {
    isLoading.value = false;
  }
}

onMounted(async () => {
  await fetchProjects();
  await fetchCertificates();
  await fetchSkills();
  await fetchContacts();
});
</script>

<template>
  <div class="space-y-8">
    <div class="bg-white border-4 border-black p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] mb-8">
      <h1 class="text-4xl font-black mb-2 uppercase">Welcome back, Admin! 👋</h1>
      <p class="font-mono text-gray-600">Here represents the statistical summary of your portfolio backend.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div
        class="bg-blue-100 border-4 border-black p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[-4px] hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] transition-all">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-bold text-sm uppercase mb-1">Total Projects</p>
            <h3 class="text-5xl font-black">{{ isLoading ? "..." : projects }}</h3>
          </div>
          <span class="text-4xl">🚀</span>
        </div>
        <div class="mt-4 pt-4 border-t-2 border-black border-dashed text-xs font-mono">Updated recently</div>
      </div>

      <div
        class="bg-green-100 border-4 border-black p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[-4px] hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] transition-all">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-bold text-sm uppercase mb-1">Certificates</p>
            <h3 class="text-5xl font-black">{{ isLoading ? "..." : certificates }}</h3>
          </div>
          <span class="text-4xl">🏆</span>
        </div>
        <div class="mt-4 pt-4 border-t-2 border-black border-dashed text-xs font-mono">Valid credentials</div>
      </div>

      <div
        class="bg-yellow-100 border-4 border-black p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[-4px] hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] transition-all">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-bold text-sm uppercase mb-1">Tech Stack</p>
            <h3 class="text-5xl font-black">{{ isLoading ? "..." : skills }}</h3>
          </div>
          <span class="text-4xl">⚡</span>
        </div>
        <div class="mt-4 pt-4 border-t-2 border-black border-dashed text-xs font-mono">Active skills</div>
      </div>

      <div
        class="bg-pink-100 border-4 border-black p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:translate-y-[-4px] hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] transition-all">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-bold text-sm uppercase mb-1">Inbox Messages</p>
            <h3 class="text-5xl font-black">{{ isLoading ? "..." : contacts }}</h3>
          </div>
          <span class="text-4xl">📬</span>
        </div>
        <div class="mt-4 pt-4 border-t-2 border-black border-dashed text-xs font-mono">Check your email</div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
      <div class="bg-white border-4 border-black p-5 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
        <h3 class="font-black text-xl mb-4 border-b-2 border-black pb-2">SYSTEM STATUS</h3>
        <ul class="space-y-2 font-mono text-sm">
          <li class="flex justify-between">
            <span>Backend Connection:</span>
            <span class="text-green-600 font-bold">ONLINE (Laravel)</span>
          </li>
          <li class="flex justify-between">
            <span>Database:</span>
            <span class="text-blue-600 font-bold">CONNECTED (MySQL)</span>
          </li>
          <li class="flex justify-between">
            <span>Last Login:</span>
            <span>Today, 10:00 AM</span>
          </li>
        </ul>
      </div>

      <div class="bg-white border-4 border-black p-5 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
        <h3 class="font-black text-xl mb-4 border-b-2 border-black pb-2">PENDING TASKS</h3>
        <div class="bg-gray-100 p-4 border-2 border-gray-300 text-center text-gray-500 italic">
          No pending approvals for projects.
        </div>
      </div>
    </div>
  </div>
</template>
