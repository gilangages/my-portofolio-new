<script setup>
import { reactive } from "vue";
import { adminLogin } from "../llib/api/adminApi";
import { useLocalStorage } from "@vueuse/core";
import { useRouter } from "vue-router";
import { alertError } from "../llib/alert";

async function handleSubmit() {
  const token = useLocalStorage("token", "");
  const admin = reactive({
    email = "",
    password= ""
  });
  const router = useRouter()

  const response = await adminLogin(admin);
  const responseBody = await response.json()
  console.log(responseBody);

  if(response.status === 200){
    token.value = responseBody.token;
    await router.push({
      path: '/admin/dashboard'
    })
  }else{
    await alertError(responseBody.errors)
  }

}
</script>
<template>login admin</template>
