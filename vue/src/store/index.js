import { createStore } from "vuex";
import axiosClient from "../axios";

const store = createStore({
  state: {
    user: {
      data: {},
      token: sessionStorage.getItem("TOKEN"),
    },
    currentSurvey: {
      loading: false,
      data: {},
    },
    surveys: {
      loading: false,
      links: [],
      data: [],
    },
    questionTypes: ["text", "select", "radio", "checkbox", "textarea"],
    notification: {
      show: false,
      type: null,
      message: null,
    },
  },

  getters: {},

  actions: {
    // get all surveys
    getSurveys({ commit }, { url = null } = {}) {
      url = url || "/survey";
      commit("setSurveysLoading", true);
      return axiosClient
        .get(url)
        .then((response) => {
          commit("setSurveysLoading", false);
          commit("setSurveys", response.data);
          return response;
        })
        .catch((error) => {
          commit("setSurveysLoading", false);
          throw error;
        });
    },

    // get specific survey
    getSurvey({ commit }, id) {
      commit("setCurrentSurveyLoading", true);
      return axiosClient
        .get(`/survey/${id}`)
        .then((response) => {
          commit("setCurrentSurvey", response.data);
          commit("setCurrentSurveyLoading", false);
          return response;
        })
        .catch((error) => {
          commit("setCurrentSurveyLoading", false);
          throw error;
        });
    },

    // get survey by slug
    getSurveyBySlug({ commit }, slug) {
      commit("setCurrentSurveyLoading", true);
      return axiosClient
        .get(`/survey-by-slug/${slug}`)
        .then((response) => {
          commit("setCurrentSurvey", response.data);
          commit("setCurrentSurveyLoading", false);
          return response;
        })
        .catch((error) => {
          commit("setCurrentSurveyLoading", false);
          throw error;
        });
    },

    // save survey
    saveSurvey({ commit }, survey) {
      delete survey.image_url;
      let response;
      if (survey.id) {
        response = axiosClient
          .put(`/survey/${survey.id}`, survey)
          .then((res) => {
            commit("setCurrentSurvey", res.data);
            return res;
          });
      } else {
        response = axiosClient.post("/survey/", survey).then((res) => {
          commit("setCurrentSurvey", res.data);
          return res;
        });
      }
      return response;
    },

    // save survey answers
    saveSurveyAnswer({ commit }, { surveyId, answers }) {
      return axiosClient.post(`/survey/${surveyId}/answer`, { answers });
    },

    deleteSurvey: ({}, id) => {
      return axiosClient.delete(`/survey/${id}`);
    },

    // auth function
    register({ commit }, user) {
      return axiosClient.post("/register", user).then(({ data }) => {
        commit("setUser", data.user);
        commit("setToken", data.token);
        return data;
      });
    },

    login({ commit }, user) {
      return axiosClient.post("/login", user).then(({ data }) => {
        commit("setUser", data.user);
        commit("setToken", data.token);
        return data;
      });
    },

    logout({ commit }) {
      return axiosClient.post("/logout").then((response) => {
        commit("logout");
        return response;
      });
    },

    /*register({ commit }, user) {
      return fetch(`http://localhost:8000/api/register`, {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        method: "POST",
        body: JSON.stringify(user),
      })
        .then((response) => response.json())
        .then((response) => {
          commit("setUser", response);
          return response;
        });
    },*/
  },

  mutations: {
    setSurveysLoading: (state, loading) => {
      state.surveys.loading = loading;
    },

    setSurveys: (state, surveys) => {
      state.surveys.links = surveys.meta.links;
      state.surveys.data = surveys.data;
    },

    setCurrentSurveyLoading: (state, loading) => {
      state.currentSurvey.loading = loading;
    },

    setCurrentSurvey: (state, survey) => {
      state.currentSurvey.data = survey.data;
    },

    logout: (state) => {
      (state.user.data = {}),
        (state.user.token = null),
        sessionStorage.removeItem("TOKEN");
    },

    setUser: (state, user) => {
      state.user.data = user;
    },

    setToken: (state, token) => {
      state.user.token = token;
      sessionStorage.setItem("TOKEN", token);
    },

    notify: (state, { message, type }) => {
      state.notification.show = true;
      state.notification.type = type;
      state.notification.message = message;
      setTimeout(() => {
        state.notification.show = false;
      }, 3000);
    },
  },

  modules: {},
});

export default store;
