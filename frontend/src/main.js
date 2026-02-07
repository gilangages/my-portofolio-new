import { createApp } from "vue";
import "./style.css";
import App from "./App.vue";
import { createRouter, createWebHistory } from "vue-router";
import Homepage from "./components/Homepage/Homepage.vue";
import DashboardAdmin from "./components/LayoutAdmin/DashboardAdmin.vue";
import AdminUploadProject from "./components/Admin/AdminUploadProject.vue";
import AdminLogin from "./components/Admin/AdminLogin.vue";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: "/",
      component: Homepage,
    },
    {
      path: "/admin/login",
      component: AdminLogin,
    },
    {
      path: "/admin/dashboard",
      component: DashboardAdmin,
      children: [
        {
          path: "projects",
          component: AdminUploadProject,
        },
      ],
    },
  ],
});

createApp(App).use(router).mount("#app");
