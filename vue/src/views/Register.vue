<template>
    <div>
        <img
            class="mx-auto h-12 w-auto"
            src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg"
            alt="Workflow"
        />
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Register to your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Or
            {{ " " }}
            <router-link
                :to="{ name: 'Login' }"
                class="font-medium text-indigo-600 hover:text-indigo-500"
                >Log in now
            </router-link>
        </p>
    </div>
    <form class="mt-8 space-y-6" @submit="register">
        <div
            v-if="errorMsg"
            class="flex justify-between bg-red-500 text-white py-2 px-4 pr-0 rounded font-bold mb-4 shadow-lg"
        >
            <div>
                <div v-for="(value, key) in errorMsg" :key="key">
                    <p v-for="error in value" :key="error" class="text-sm">
                        {{ error }}
                    </p>
                </div>
            </div>
            <span @click="errorMsg = ''" class="cursor-pointer">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 mr-2"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </span>
        </div>

        <input type="hidden" name="remember" value="true" />
        <div class="rounded-md shadow-sm -space-y-px">
            <div>
                <label for="full-name" class="sr-only">Full Name</label>
                <input
                    id="full-name"
                    name="full-name"
                    type="full-name"
                    autocomplete="full-name"
                    required=""
                    v-model="user.name"
                    class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                    placeholder="Full Name"
                />
            </div>
            <div>
                <label for="email-address" class="sr-only">Email address</label>
                <input
                    id="email-address"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required=""
                    v-model="user.email"
                    class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                    placeholder="Email address"
                />
            </div>
            <div>
                <label for="password" class="sr-only">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required=""
                    v-model="user.password"
                    class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                    placeholder="Password"
                />
            </div>

            <div>
                <label for="password_confirmation" class="sr-only"
                    >Password Confirmation</label
                >
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="current-password_confirmation"
                    required=""
                    v-model="user.password_confirmation"
                    class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                    placeholder="Password Confirmation"
                />
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input
                    id="remember-me"
                    name="remember-me"
                    type="checkbox"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label
                    for="remember-me"
                    class="ml-2 block text-sm text-gray-900"
                >
                    Remember me
                </label>
            </div>
        </div>

        <div>
            <button
                type="submit"
                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <LockClosedIcon
                        class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400"
                        aria-hidden="true"
                    />
                </span>

                <svg
                    v-if="loading"
                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                </svg>
                Register
            </button>
        </div>
    </form>
</template>

<script setup>
import { LockClosedIcon } from "@heroicons/vue/solid";
import { ref } from "vue";
import { useRouter } from "vue-router";
import store from "../store";

const user = {
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
};

const router = useRouter();

let loading = ref(false);
let errorMsg = ref("");

function register(event) {
    event.preventDefault();
    loading.value = true;

    store
        .dispatch("register", user)
        .then(() => {
            loading.value = false;
            router.push({
                name: "Dashboard",
            });
        })
        .catch((err) => {
            loading.value = false;
            errorMsg.value = err.response.data.error;
        });
}
</script>
