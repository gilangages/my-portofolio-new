import { createApp } from "vue";
import "./style.css";
import App from "./App.vue";
import { createRouter, createWebHistory } from "vue-router";
import Homepage from "./components/Homepage/Homepage.vue";
import DashboardAdmin from "./components/LayoutAdmin/DashboardAdmin.vue";
import AdminUploadProject from "./components/Admin/Pages/AdminUploadProject.vue";
import AdminDashboard from "./components/Admin/Pages/AdminDashboard.vue";
import AdminLogin from "./components/Admin/AdminLogin.vue";
import AdminLogout from "./components/Admin/AdminLogout.vue";
import AdminSkills from "./components/Admin/Pages/AdminSkills.vue";

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
          path: "",
          component: AdminDashboard,
        },
        {
          path: "logout",
          component: AdminLogout,
        },
        {
          path: "projects",
          component: AdminUploadProject,
        },
        {
          path: "skills",
          component: AdminSkills,
        },
      ],
    },
  ],
});

createApp(App).use(router).mount("#app");
