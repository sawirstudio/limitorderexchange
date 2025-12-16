<script setup lang="ts">
import { createPersonalAccessToken, deletePersonalAccessToken, type CreatePersonalAccessTokenRequest, type Profile } from '../api';
import { useForm } from '@tanstack/vue-form';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { type HTTPError } from 'ky';
import { useTemplateRef } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    profile?: {data: Profile}
}>()

const queryClient = useQueryClient();

const dialog = useTemplateRef<HTMLDialogElement>("dialog");

const loginForm = useForm({
    defaultValues: {
        email: "",
        password: "",
    },
    onSubmit: async ({ value }) => {
        loginMutation.mutate(value);
    },
});

const loginMutation = useMutation({
    onSuccess: ({ data }) => {
        localStorage.setItem("personal_access_tokens", data.token);
        dialog.value?.close();
        toast.success("Login Success");
        loginForm.reset();
        queryClient.invalidateQueries({ queryKey: ["profile"] });
        queryClient.invalidateQueries({ queryKey: ["orderbook"] });
        queryClient.invalidateQueries({ queryKey: ["pastorder"] });
    },
    onError: (error: HTTPError) => {
        toast.error(error.message);
        error.response
            .json<{
                errors: { email: string[]; password: string[] };
                message: string;
            }>()
            .then((data) => {
                loginForm.setErrorMap({
                    onChange: {
                        fields: data.errors,
                    },
                });
            });
    },
    mutationFn: (json: CreatePersonalAccessTokenRequest) =>
        createPersonalAccessToken(json),
});

const logoutMutation = useMutation({
    mutationFn: () => deletePersonalAccessToken(),
    onSuccess: () => {
        localStorage.removeItem("personal_access_tokens");
        queryClient.invalidateQueries({ queryKey: ["profile"] });
        queryClient.invalidateQueries({ queryKey: ["orderbook"] });
        queryClient.invalidateQueries({ queryKey: ["pastorder"] });
        toast.success("Logout Success");
    },
});
</script>
<style scoped>
dialog::backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}
</style>
<template>
    <nav class="flex flex-row gap-2 justify-between py-4 px-4">
        <div class="">Exchangemarket</div>
    <div class="">
        <div>
        <button
            v-if="!profile"
            @click="dialog?.showModal()"
        >
            Login
        </button>
        <button v-else @click="logoutMutation.mutate()">Logout</button>
        </div>
    </div>
    </nav>
    <dialog ref="dialog">
        <div
            class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-lg p-8"
        >
            <form @submit.prevent.stop="loginForm.handleSubmit">
                <div class="flex flex-col gap-2 p-8">
                    <loginForm.Field name="email">
                        <template v-slot="{ field }">
                            <div>
                                <label
                                    :for="field.name"
                                    class="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                    >Email</label
                                >
                                <div class="mt-2 grid grid-cols-1">
                                    <input
                                        id="email"
                                        type="email"
                                        :name="field.name"
                                        :value="field.state.value"
                                        @blur="field.handleBlur"
                                        @input="(e) => field.handleChange((e.target as HTMLInputElement).value)"
                                        placeholder="you@example.com"
                                        :aria-invalid="true"
                                        aria-describedby="email-error"
                                        :class="[
                                            'col-start-1 row-start-1 block w-full rounded-md bg-white py-1.5 pl-3 pr-10 text-red-900 outline outline-1 -outline-offset-1 outline-red-300 placeholder:text-red-300 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-red-600 sm:pr-9 sm:text-sm/6 dark:bg-white/5 dark:text-red-400 dark:outline-red-500/50 dark:placeholder:text-red-400/70 dark:focus:outline-red-400',
                                            field.state.meta.isValid
                                                ? 'focus:outline-gray-600 text-gray-500'
                                                : 'focus:outline-red-600 text-red-500',
                                        ]"
                                    />
                                    <svg
                                        v-if="!field.state.meta.isValid"
                                        viewBox="0 0 16 16"
                                        fill="currentColor"
                                        data-slot="icon"
                                        aria-hidden="true"
                                        class="pointer-events-none col-start-1 row-start-1 mr-3 size-5 self-center justify-self-end text-red-500 sm:size-4 dark:text-red-400"
                                    >
                                        <path
                                            d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14ZM8 4a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3A.75.75 0 0 1 8 4Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"
                                            clip-rule="evenodd"
                                            fill-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <p
                                    v-if="!field.state.meta.isValid"
                                    id="email-error"
                                    class="mt-2 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ field.state.meta.errors.join(", ") }}
                                </p>
                            </div>
                        </template>
                    </loginForm.Field>
                    <loginForm.Field name="password">
                        <template v-slot="{ field }">
                            <div>
                                <label
                                    for="password"
                                    class="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                    >Password</label
                                >
                                <div class="mt-2 grid grid-cols-1">
                                    <input
                                        id="password"
                                        type="password"
                                        :name="field.name"
                                        :value="field.state.value"
                                        @blur="field.handleBlur"
                                        @input="(e) => field.handleChange((e.target as HTMLInputElement).value)"
                                        aria-invalid="true"
                                        aria-describedby="email-error"
                                        class="col-start-1 row-start-1 block w-full rounded-md bg-white py-1.5 pl-3 pr-10 text-red-900 outline outline-1 -outline-offset-1 outline-red-300 placeholder:text-red-300 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-red-600 sm:pr-9 sm:text-sm/6 dark:bg-white/5 dark:text-red-400 dark:outline-red-500/50 dark:placeholder:text-red-400/70 dark:focus:outline-red-400"
                                    />
                                    <svg
                                        v-if="!field.state.meta.isValid"
                                        viewBox="0 0 16 16"
                                        fill="currentColor"
                                        data-slot="icon"
                                        aria-hidden="true"
                                        class="pointer-events-none col-start-1 row-start-1 mr-3 size-5 self-center justify-self-end text-red-500 sm:size-4 dark:text-red-400"
                                    >
                                        <path
                                            d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14ZM8 4a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3A.75.75 0 0 1 8 4Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"
                                            clip-rule="evenodd"
                                            fill-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <p
                                    v-if="!field.state.meta.isValid"
                                    "
                                    id="password-error"
                                    class="mt-2 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ field.state.meta.errors.join(", ") }}
                                </p>
                            </div>
                        </template>
                    </loginForm.Field>
                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:shadow-none dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"

                        v-bind:disabled="loginMutation.isPending.value"
                        >Sign in</button>
                    </div>
                    <button type="button" @click="dialog?.close()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </dialog>
</template>
