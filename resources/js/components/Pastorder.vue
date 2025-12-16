<script setup lang="ts">
import { cancelOrder, getOrders, type OrderSymbol } from "../api";
import { useMutation, useQuery, useQueryClient } from "@tanstack/vue-query";
import { ref, watch } from "vue";
import { toast } from "vue-sonner";

const symbol = ref<OrderSymbol | undefined>(undefined);
const side = ref<number | undefined>(undefined);
const status = ref<number | undefined>(undefined);

watch([symbol, side, status], () => ordersQuery.refetch());

const queryClient = useQueryClient();

const ordersQuery = useQuery({
    queryKey: [
        "pastorder",
        { symbol: symbol.value, side: side.value, status: status.value },
    ],
    queryFn: () =>
        getOrders({
            symbol: symbol.value ?? "",
            side: side.value?.toString() ?? "",
            status: status.value?.toString() ?? "",
        }),
});

const cancelOrderMutation = useMutation({
    onSuccess: () => {
        toast("Success cancel order");
        queryClient.invalidateQueries({ queryKey: ["orderbook"] });
        queryClient.invalidateQueries({ queryKey: ["pastorder"] });
        queryClient.invalidateQueries({ queryKey: ["profile"] });
    },
    mutationFn: ({ orderId }: { orderId: number | string }) =>
        cancelOrder(orderId),
});
</script>

<template>
    <div>
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1
                    class="text-base font-semibold text-gray-900 dark:text-white"
                >
                    Past Orders
                </h1>
                <!-- <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                        Orderbook
                    </p> -->
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <div class="gap-2 flex">
                    <div class="flex flex-col">
                        <label for="symbol">Symbol</label>
                        <select
                            v-model="symbol"
                            name="symbol"
                            placeholder="Choose Symbol"
                            class="py-2 px-1 border border-gray-300 rounded-md text-xs"
                        >
                            <option value="">Choose Symbol</option>
                            <option value="BTC">BTC</option>
                            <option value="ETH">ETH</option>
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label for="side">Side</label>
                        <select
                            v-model="side"
                            name="side"
                            placeholder="Choose Side"
                            class="py-2 px-1 border border-gray-300 rounded-md text-xs"
                        >
                            <option value="">Choose Side</option>
                            <option :value="0">Sell</option>
                            <option :value="1">Buy</option>
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label for="status">Status</label>
                        <select
                            v-model="status"
                            name="status"
                            placeholder="Choose Status"
                            class="py-2 px-1 border border-gray-300 rounded-md text-xs"
                        >
                            <option value="">Choose Status</option>
                            <option :value="1">OPEN</option>
                            <option :value="2">FILLED</option>
                            <option :value="3">CANCELLED</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="ordersQuery.isFetching.value" class="mt-8 flow-root">
            Loading data...
        </div>
        <div v-if="ordersQuery.isFetched.value" class="mt-8 flow-root">
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
                                    Symbol
                                </th>
                                <th
                                    scope="col"
                                    class="px-2 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-white"
                                >
                                    Side
                                </th>
                                <th
                                    scope="col"
                                    class="px-2 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-white"
                                >
                                    Price
                                </th>
                                <th
                                    scope="col"
                                    class="px-2 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-white"
                                >
                                    Amount
                                </th>
                                <th
                                    scope="col"
                                    class="px-2 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-white"
                                >
                                    Status
                                </th>
                                <th
                                    scope="col"
                                    class="px-2 py-3.5 text-left text-sm font-semibold whitespace-nowrap text-gray-900 dark:text-white"
                                >
                                    Time
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900"
                        >
                            <tr
                                v-for="order in ordersQuery.data.value?.data"
                                :key="order.id"
                            >
                                <td
                                    class="px-2 py-2 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ order.symbol }}
                                </td>
                                <td
                                    class="px-2 py-2 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ order.side ? "BUY" : "SELL" }}
                                </td>
                                <td
                                    class="px-2 py-2 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ order.price }}
                                </td>
                                <td
                                    class="px-2 py-2 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ order.amount }}
                                </td>
                                <td
                                    class="px-2 py-2 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    <span v-if="order.status == 1">OPEN</span>
                                    <span v-if="order.status == 2">FILLED</span>
                                    <span v-if="order.status == 3"
                                        >CANCELLED</span
                                    >
                                </td>
                                <td
                                    class="px-2 py-2 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ order.created_at }}
                                </td>
                                <td
                                    class="py-2 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-0"
                                >
                                    <button
                                        type="button"
                                        class=""
                                        v-if="order.status == 1"
                                        @click="
                                            cancelOrderMutation.mutate({
                                                orderId: order.id,
                                            })
                                        "
                                    >
                                        Cancel
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
