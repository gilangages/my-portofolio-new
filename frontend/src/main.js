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
import AdminUploadOrUpdateProfile from "./components/Admin/Pages/Profile/AdminUploadOrUpdateProfile.vue";
import AdminCertificateList from "./components/Admin/Pages/Certificate/AdminCertificateList.vue";
import AdminUploadOrUpdateCertificate from "./components/Admin/Pages/Certificate/AdminUploadOrUpdateCertificate.vue";
import AdminExperienceList from "./components/Admin/Pages/Experience/AdminExperienceList.vue";
import AdminServiceList from "./components/Admin/Pages/Service/AdminServiceList.vue";

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

        //certificate
        {
          path: "certificates",
          component: AdminCertificateList,
        },
        {
          path: "certificates/create",
          component: AdminUploadOrUpdateCertificate,
        },
        {
          path: "certificates/edit/:id",
          component: AdminUploadOrUpdateCertificate,
        },

        //contact
        {
          path: "contacts",
          component: AdminContactList,
        },

        //experience
        {
          path: "experiences",
          component: AdminExperienceList,
        },

        //skills
        {
          path: "skills",
          component: AdminSkillsList,
        },

        //service
        {
          path: "services",
          component: AdminServiceList,
        },

        //profile
        {
          path: "profile",
          component: AdminUploadOrUpdateProfile,
        },
      ],
    },
  ],
});

createApp(App).use(router).mount("#app");
