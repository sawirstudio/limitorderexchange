<script setup lang="ts">
import { ref } from "vue";
import { createOrder, type Profile, type CreateOrderRequest } from "../api";
import { useEcho } from "@laravel/echo-vue";
import { useForm } from "@tanstack/vue-form";
import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { toast } from "vue-sonner";

const props = defineProps<{ profile?: { data: Profile } }>();
const profileData = ref(props.profile?.data);
const queryClient = useQueryClient();
useEcho(`private-user.${props.profile?.data.id}`, ["OrderMatched"], (e) => {
    console.log(e)
    profileData.value = e.order.user
});

const orderForm = useForm({
    defaultValues: {
        symbol: "BTC",
        side: 1,
        price: 0,
        amount: 0,
    },
    onSubmit: ({ value }) => {
        createOrderMutation.mutate(value);
    },
});

const createOrderMutation = useMutation({
    onSuccess: () => {
        toast("Success create order");
        orderForm.reset();
        queryClient.invalidateQueries({ queryKey: ["orderbook"] });
        queryClient.invalidateQueries({ queryKey: ["pastorder"] });
        queryClient.invalidateQueries({ queryKey: ["profile"] });
    },
    mutationFn: (data: CreateOrderRequest) => createOrder(data),
});
</script>

<template>
    <div class="p-4" v-if="profile">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
            {{ profile?.data.name }} | {{ profile?.data.balance }} USD
        </h3>
        <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div
                v-for="item in profile?.data.assets"
                :key="item.symbol"
                class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow-sm sm:p-6 dark:bg-gray-800/75 dark:inset-ring dark:inset-ring-white/10"
            >
                <dt
                    class="truncate text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                    {{ item.symbol }}
                </dt>
                <dd
                    class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white"
                >
                    {{ item.amount }}
                </dd>
            </div>
        </dl>

        <!-- Limit Order Form -->
        <div
            v-if="profile"
            class="mt-8 overflow-hidden rounded-lg bg-white px-4 py-5 shadow-sm sm:p-6 dark:bg-gray-800/75 dark:inset-ring dark:inset-ring-white/10"
        >
            <h4
                class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
            >
                Limit Order
            </h4>

            <form
                @submit.prevent.stop="orderForm.handleSubmit"
                class="space-y-4"
            >
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Symbol -->
                    <orderForm.Field name="symbol">
                        <template v-slot="{ field }">
                            <div>
                                <label
                                    for="symbol"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Symbol
                                </label>
                                <select
                                    id="symbol"
                                    :name="field.name"
                                    :value="field.state.value"
                                    @blur="field.handleBlur"
                                    @change="(e) => field.handleChange((e.target as HTMLSelectElement).value)"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2 px-3"
                                >
                                    <option value="BTC">BTC</option>
                                    <option value="ETH">ETH</option>
                                </select>
                            </div>
                        </template>
                    </orderForm.Field>

                    <!-- Side -->
                    <orderForm.Field name="side">
                        <template v-slot="{ field }">
                            <div>
                                <label
                                    for="side"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Side
                                </label>
                                <select
                                    id="side"
                                    @blur="field.handleBlur"
                                    :name="field.name"
                                    :value="field.state.value"
                                    @change="(e) => field.handleChange(Number((e.target as HTMLSelectElement).value))"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2 px-3"
                                >
                                    <option value="1">Buy</option>
                                    <option value="0">Sell</option>
                                </select>
                            </div>
                        </template>
                    </orderForm.Field>

                    <!-- Price -->
                    <orderForm.Field name="price">
                        <template v-slot="{ field }">
                            <div>
                                <label
                                    for="price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Price
                                </label>
                                <input
                                    type="number"
                                    id="price"
                                    :name="field.name"
                                    :value="field.state.value"
                                    @blur="field.handleBlur"
                                    @change="(e) => field.handleChange(Number((e.target as HTMLInputElement).value))"
                                    step="1"
                                    min="0"
                                    placeholder="0.00"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2 px-3"
                                />
                            </div>
                        </template>
                    </orderForm.Field>

                    <!-- Amount -->
                    <orderForm.Field name="amount">
                        <template v-slot="{ field }">
                            <div>
                                <label
                                    for="amount"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Amount
                                </label>
                                <input
                                    type="number"
                                    id="amount"
                                    :name="field.name"
                                    :value="field.state.value"
                                    @blur="field.handleBlur"
                                    @change="(e) => field.handleChange(Number((e.target as HTMLInputElement).value))"
                                    step="1"
                                    min="0"
                                    placeholder="0.00"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2 px-3"
                                />
                            </div>
                        </template>
                    </orderForm.Field>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    :disabled="createOrderMutation.isPending.value"
                    class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{
                        createOrderMutation.isPending.value
                            ? "Placing Order..."
                            : "Place Order"
                    }}
                </button>
            </form>
        </div>
    </div>
</template>
