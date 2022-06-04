import { createStore } from "vuex";
import axiosClient from "../axios";

const surveyTemp = [
  {
    id: 100,
    title: "the iti diploma content",
    slug: "the-iti-diploma-content",
    status: "draft",
    image:
      "https://images.unsplash.com/photo-1532522750741-628fde798c73?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80",
    description:
      "In an orchestrated ensemble of human capital development, we enable, empower and elevate P-E-O-P-L-E to strive excellence and lead the change ",
    expire_at: "2022-5-31 18:00:00",
    created_at: "2022-5-5 18:00:00",
    updated_at: "2022-5-5 18:00:00",
    questions: [
      {
        id: 1,
        type: "select",
        question: "from which country are you ?",
        description: null,
        data: {
          options: [
            { uuid: "123e4567-e89b-12d3-a456-426614174000", text: "USA" },
            { uuid: "456e4567-e89b-12d3-a456-456714174000", text: "Egypt" },
            { uuid: "789e4567-e89b-12d3-a456-789114174000", text: "England" },
            { uuid: "147e4567-e89b-12d3-a456-158414174000", text: "Germany" },
          ],
        },
      },
      {
        id: 2,
        type: "checkbox",
        question: "which language videos do you want to learn ?",
        description:
          "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,",
        data: {
          options: [
            { uuid: "178e4567-e89b-12d3-a456-426614174000", text: "PHP" },
            { uuid: "498e4567-e89b-12d3-a456-456714174000", text: "Python" },
            { uuid: "774e4567-e89b-12d3-a456-789114174000", text: "C#" },
            {
              uuid: "144e4567-e89b-12d3-a456-158414174000",
              text: "JavaScript",
            },
          ],
        },
      },
      {
        id: 3,
        type: "checkbox",
        question: "which frameworks do you want to learn ?",
        description:
          "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,",
        data: {
          options: [
            { uuid: "178e4567-e89b-12d3-a456-427714174000", text: "Laravel" },
            { uuid: "498e4567-e89b-12d3-a456-455414174000", text: "Django" },
            { uuid: "774e4567-e89b-12d3-a456-789614174000", text: "NodeJS" },
            { uuid: "144e4567-e89b-12d3-a456-152114174000", text: "Angulare" },
          ],
        },
      },
      {
        id: 4,
        type: "radio",
        question: "which laravel version do you want to learn ?",
        description:
          "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,",
        data: {
          options: [
            { uuid: "178e4567-e89b-12d3-a456-896614174000", text: "Laravel 5" },
            { uuid: "498e4567-e89b-12d3-a456-746714174000", text: "Laravel 6" },
            { uuid: "774e4567-e89b-12d3-a456-359114174000", text: "Laravel 7" },
            { uuid: "144e4567-e89b-12d3-a456-698414174000", text: "Laravel 8" },
          ],
        },
      },
      {
        id: 5,
        type: "text",
        question: "which your favourite ?",
        description: null,
        data: {},
      },
      {
        id: 6,
        type: "textarea",
        question: "which your add ?",
        description: null,
        data: {},
      },
    ],
  },
];

const store = createStore({
  state: {
    user: {
      data: {},
      token: sessionStorage.getItem("TOKEN"),
    },
    surveys: [...surveyTemp],
  },

  getters: {},

  actions: {
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
  },

  modules: {},
});

export default store;
