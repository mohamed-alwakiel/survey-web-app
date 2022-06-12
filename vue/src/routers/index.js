import { createRouter, createWebHistory } from "vue-router";

import store from "./../store";

import MainLayout from "./../components/MainLayout.vue";
import AuthLayout from "./../components/AuthLayout.vue";

import Dashboard from "./../views/Dashboard.vue";
import Register from "./../views/Register.vue";
import Login from "./../views/Login.vue";
import Survey from "./../views/Survey.vue";
import SurveyView from "./../views/SurveyView.vue";
import SurveyPublicView from "./../views/SurveyPublicView.vue";

const routes = [
  {
    path: "/",
    redirect: "/dashboard",
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      { path: "/dashboard", name: "Dashboard", component: Dashboard },
      { path: "/survey", name: "Survey", component: Survey },
      { path: "/survey/create", name: "SurveyCreate", component: SurveyView },
      { path: "/survey/:id", name: "SurveyView", component: SurveyView },
    ],
  },
  {
    path: "/view/survey/:slug",
    name: "SurveyPublicView",
    component: SurveyPublicView,
  },
  {
    path: "/auth",
    redirect: "/login",
    name: "Auth",
    component: AuthLayout,
    meta: { isGuest: true },
    children: [
      { path: "/register", name: "Register", component: Register },
      { path: "/login", name: "Login", component: Login },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !store.state.user.token) {
    next({ name: "Login" });
  } else if (store.state.user.token && to.meta.isGuest) {
    next({ name: "Dashboard" });
  } else {
    next();
  }
});

export default router;
