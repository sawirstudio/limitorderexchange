<script setup lang="ts">
import { Toaster } from "vue-sonner";
import { computed, ref } from "vue";
import { getProfile } from "./api";
import Header from "./components/Header.vue";
import Profile from "./components/Profile.vue";
import Orderbook from "./components/Orderbook.vue";
import Pastorder from "./components/Pastorder.vue";
import { useQuery } from "@tanstack/vue-query";

const profileQuery = useQuery({
    queryKey: ["profile"],
    queryFn: () => getProfile(),
});

const tabs = computed(() =>
    [
        { name: "Orderbook", href: "#orderbook", auth: undefined },
        { name: "Past Orders", href: "#past-orders", auth: true },
    ].filter(
        (tab) =>
            tab.auth === undefined || (!!profileQuery.data.value && tab.auth)
    )
);
const selectedTab = ref("#orderbook");
</script>

<template>
    <Toaster />
    <Header :profile="profileQuery.data.value" />
    <Profile
        v-if="profileQuery.data.value"
        :profile="profileQuery.data.value"
    />
    <div>
        <div class="grid grid-cols-1 sm:hidden">
            <!-- Use an "onChange" listener to redirect the user to the selected tab URL. -->
            <select
                @change="(e) => selectedTab = (e.target as HTMLSelectElement).value"
                aria-label="Select a tab"
                class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-2 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 dark:bg-white/5 dark:text-gray-100 dark:outline-white/10 dark:*:bg-gray-800 dark:focus:outline-indigo-500"
            >
                <option
                    v-for="tab in tabs"
                    :key="tab.name"
                    :selected="tab.href === selectedTab"
                    :value="tab.href"
                >
                    {{ tab.name }}
                </option>
            </select>
            <!-- <ChevronDownIcon
                class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end fill-gray-500 dark:fill-gray-400"
                aria-hidden="true"
            /> -->
        </div>
        <div class="hidden sm:block">
            <div class="border-b border-gray-200 dark:border-white/10">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a
                        v-for="tab in tabs"
                        :key="tab.name"
                        :href="tab.href"
                        @click="selectedTab = tab.href"
                        :class="[
                            tab.href === selectedTab
                                ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-white/20 dark:hover:text-gray-200',
                            'border-b-2 px-1 py-4 text-sm font-medium whitespace-nowrap',
                        ]"
                        :aria-current="true ? 'page' : undefined"
                        >{{ tab.name }}</a
                    >
                </nav>
            </div>
        </div>
    </div>
    <div class="px-4 mt-6">
        <Orderbook v-if="selectedTab === '#orderbook'" />
        <Pastorder v-if="selectedTab === '#past-orders'" />
    </div>
    <div class="h-[300px]"></div>
</template>
