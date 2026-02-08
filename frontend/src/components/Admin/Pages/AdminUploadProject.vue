<script setup>
import { useLocalStorage } from "@vueuse/core";
import { reactive, ref } from "vue";
import { adminUploadProject } from "../../llib/api/ProjectApi";
import { alertSuccess } from "../../llib/alert";
import { alertError } from "../../llib/alert";

const token = useLocalStorage("token", "");
const form = reactive({
  title: "",
  description: "",
  repository_link: "",
  live_demo_link: "",
  tech_stack_ids: "",
});
const file = ref(null);
const previewImage = ref(null);
const isLoading = ref(false);

const handleFileChange = (e) => {
  const selectedFile = e.target.files[0];
  if (selectedFile) {
    file.value = selectedFile;
    // Buat URL sementara untuk preview gambar
    previewImage.value = URL.createObjectURL(selectedFile);
  }
};

async function handleSubmit() {
  if (!form.title || !file.value) {
    alertError("Judul dan Gambar wajib diisi!");
    return;
  }

  isLoading.value = true;

  try {
    const formData = new FormData();
    formData.append("title", form.title);
    formData.append("description", form.description);
    formData.append("repository_link", form.repository_link);
    formData.append("live_demo_link", form.live_demo_link);
    formData.append("thumbnail", file.value);
    formData.append("tech_stack_ids", form.tech_stack_ids);

    const response = await adminUploadProject(token.value, formData);
    const responseBody = await response.json();
    console.log(responseBody);

    if (response.status === 201) {
      await alertSuccess("Project berhasil diupload!");
      // Reset form
      Object.keys(form).forEach((key) => (form[key] = ""));
      file.value = null;
      previewImage.value = null;
      // Reset input file element
      document.querySelector('input[type="file"]').value = "";
    } else {
      await alertError(responseBody.message);
    }
  } catch (e) {
    console.error(e);
  } finally {
    isLoading.value = false;
  }
}
</script>
<template>
  <div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-3xl font-black mb-8 border-b-4 border-black inline-block pb-2">UPLOAD NEW PROJECT</h1>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <div>
            <label class="block font-bold mb-1">
              Project Title
              <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.title"
              type="text"
              class="w-full p-3 border-2 border-black rounded shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus:translate-x-[2px] focus:translate-y-[2px] focus:shadow-none transition-all"
              placeholder="Ex: E-Commerce App" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-bold mb-1">Github Link</label>
              <input
                v-model="form.link_github"
                type="url"
                class="input-neobrutalism"
                placeholder="https://github.com/..." />
            </div>
            <div>
              <label class="block font-bold mb-1">Demo Link</label>
              <input v-model="form.link_demo" type="url" class="input-neobrutalism" placeholder="https://..." />
            </div>
          </div>

          <div>
            <label class="block font-bold mb-1">Tech Stack</label>
            <input
              v-model="form.tech_stack"
              type="text"
              class="input-neobrutalism"
              placeholder="Vue, Laravel, Tailwind..." />
            <p class="text-xs text-gray-500 mt-1">*Pisahkan dengan koma</p>
          </div>

          <!-- <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-bold mb-1">Start Date</label>
              <input v-model="form.start_date" type="date" class="input-neobrutalism" />
            </div>
            <div>
              <label class="block font-bold mb-1">End Date</label>
              <input v-model="form.end_date" type="date" class="input-neobrutalism" />
            </div>
          </div> -->
        </div>

        <div class="space-y-4">
          <div>
            <label class="block font-bold mb-1">Description</label>
            <textarea
              v-model="form.description"
              rows="5"
              class="w-full p-3 border-2 border-black rounded shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus:translate-x-[2px] focus:translate-y-[2px] focus:shadow-none transition-all"
              placeholder="Explain details about the project..."></textarea>
          </div>

          <div>
            <label class="block font-bold mb-1">
              Thumbnail
              <span class="text-red-500">*</span>
            </label>
            <div
              class="border-2 border-dashed border-black p-6 rounded-lg text-center cursor-pointer hover:bg-gray-50 relative"
              @click="$refs.fileInput.click()">
              <input ref="fileInput" type="file" class="hidden" accept="image/*" @change="handleFileChange" />

              <div v-if="!previewImage" class="flex flex-col items-center gap-2">
                <Icon icon="lucide:image-plus" class="text-4xl text-gray-400" />
                <span class="text-sm font-bold text-gray-500">Click to upload image</span>
              </div>

              <div v-else class="relative">
                <img :src="previewImage" class="w-full h-48 object-cover rounded border border-black" />
                <button
                  @click.stop="
                    previewImage = null;
                    file = null;
                  "
                  class="absolute -top-2 -right-2 bg-red-500 text-white p-1 rounded-full border border-black text-xs hover:scale-110 transition-transform">
                  <Icon icon="lucide:x" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <button
        type="submit"
        :disabled="isLoading"
        class="w-full md:w-auto px-8 py-3 bg-green-400 font-black border-2 border-black rounded shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-none active:bg-green-500 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
        <span v-if="isLoading">UPLOADING...</span>
        <span v-else class="flex items-center gap-2">
          <Icon icon="lucide:upload-cloud" />
          UPLOAD PROJECT
        </span>
      </button>
    </form>
  </div>
</template>
<style scoped></style>
