import { createApp } from "vue";
import "./style.css";
import App from "./App.vue";
import { createRouter, createWebHistory } from "vue-router";
import Homepage from "./components/Homepage/Homepage.vue";
import DashboardAdmin from "./components/LayoutAdmin/DashboardAdmin.vue";
// import AdminUploadOrUpdateProject from "./components/Admin/Pages/AdminUploadOrUpdateProject.vue";
import AdminDashboard from "./components/Admin/Pages/AdminDashboard.vue";
import AdminLogin from "./components/Admin/AdminLogin.vue";
import AdminLogout from "./components/Admin/AdminLogout.vue";
import AdminSkillsList from "./components/Admin/Pages/Skill/AdminSkillList.vue";
import AdminProjectList from "./components/Admin/Pages/Project/AdminProjectList.vue";
import AdminUploadOrUpdateProject from "./components/Admin/Pages/Project/AdminUploadOrUpdateProject.vue";
import AdminContactList from "./components/Admin/Pages/Contact/AdminContactList.vue";

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

        //projects
        {
          path: "projects",
          component: AdminProjectList,
        },
        {
          path: "projects/create",
          component: AdminUploadOrUpdateProject,
        },
        {
          path: "projects/edit/:id",
          component: AdminUploadOrUpdateProject,
        },

        //contact
        {
          path: "contacts",
          component: AdminContactList,
        },
        {
          path: "skills",
          component: AdminSkillsList,
        },
      ],
    },
  ],
});

createApp(App).use(router).mount("#app");
