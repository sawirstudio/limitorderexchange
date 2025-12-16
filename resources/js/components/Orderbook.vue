<script setup lang="ts">
import { getOrderbook, type OrderSymbol } from "../api";
import { useQuery } from "@tanstack/vue-query";
import { ref } from "vue";

const symbol = ref<OrderSymbol>("BTC");

const orderbookQuery = useQuery({
    queryKey: ["orderbook", symbol.value],
    queryFn: () => getOrderbook(symbol.value),
});
</script>
<template>
    <div>
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1
                    class="text-base font-semibold text-gray-900 dark:text-white"
                >
                    Orderbook for {{ symbol }}
                </h1>
                <!-- <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                        Orderbook
                    </p> -->
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <button
                    type="button"
                    @click="
                        {
                            symbol = symbol === 'BTC' ? 'ETH' : 'BTC';
                            orderbookQuery.refetch();
                        }
                    "
                    class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"
                >
                    Change Symbol
                </button>
            </div>
        </div>
        <div v-if="orderbookQuery.isFetching.value" class="mt-8 flow-root">
            Loading data...
        </div>
        <div v-if="orderbookQuery.isFetched.value" class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div
                    class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8"
                >
                    <table
                        class="relative min-w-full divide-y divide-gray-300 dark:divide-white/15"
                    >
                        <thead>
                            <tr>
                                <th
                                    scope="col"
                                    class="px-2 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-white"
                                >
                                    #
                                </th>
                                <th
                                    scope="col"
                                    class="px-2 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-white"
                                >
                                    Buy Amount
                                </th>

                                <th
                                    scope="col"
                                    class="px-2 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-white"
                                >
                                    Buy Cumulative Amount
                                </th>
                                <th
                                    scope="col"
                                    class="px-2 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-white"
                                >
                                    Buy Price
                                </th>
                                <th
                                    scope="col"
                                    class="px-2 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-white"
                                >
                                    Sell Price
                                </th>
                                <th
                                    scope="col"
                                    class="px-2 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-white"
                                >
                                    Sell Cumulative Amount
                                </th>
                                <th
                                    scope="col"
                                    class="px-2 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-white"
                                >
                                    Sell Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900"
                        >
                            <tr
                                v-for="order in orderbookQuery.data.value?.data"
                                :key="order.level"
                            >
                                <td
                                    class="px-2 py-2 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ order.level }}
                                </td>
                                <td
                                    class="px-2 py-2 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ order.buy_amount }}
                                </td>
                                <td
                                    class="px-2 py-2 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ order.buy_cumulative_amount }}
                                </td>
                                <td
                                    class="px-2 py-2 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ order.buy_price }}
                                </td>
                                <td
                                    class="px-2 py-2 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ order.sell_price }}
                                </td>
                                <td
                                    class="px-2 py-2 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ order.sell_cumulative_amount }}
                                </td>
                                <td
                                    class="px-2 py-2 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ order.sell_amount }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
