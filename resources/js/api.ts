import ky from "ky";

type Collection<T> = {
    data: T[];
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
};

export type OrderSymbol = "BTC" | "ETH" | "USDT" | "USDC" | "USDS" | "USDS";

type Asset = {
    symbol: OrderSymbol;
    amount: string;
    locked_amount: string;
};

const httpClient = ky.extend({
    prefixUrl: "https://virgosoft-orderexchange.test/api",
    headers: {
        Accept: "application/json",
        "X-Platform": "web",
        "X-Requested-With": "XMLHttpRequest",
        "X-Timezone": Intl.DateTimeFormat().resolvedOptions().timeZone,
    },
    throwHttpErrors: true,
    hooks: {
        beforeRequest: [
            (options) => {
                options.headers.set(
                    "Authorization",
                    `Bearer ${localStorage.getItem("personal_access_tokens")}`
                );
            },
        ],
    },
});

export type Profile = {
    id: string | number;
    name: string;
    balance: string;
    assets: Asset[];
};

export const getProfile = () =>
    httpClient.get("profile").json<{ data: Profile }>();

export const createPersonalAccessToken = (json: {
    email: string;
    password: string;
}) =>
    httpClient
        .post("personal-access-tokens", { json })
        .json<{ data: { token: string } }>();

export type CreatePersonalAccessTokenRequest = Parameters<
    typeof createPersonalAccessToken
>[0];

export const deletePersonalAccessToken = () =>
    httpClient.delete("personal-access-token");

type Order = {
    id: number | string;
    side: boolean;
    symbol: OrderSymbol;
    price: string;
    amount: string;
    status: number;
    created_at: string;
};

export const getOrders = (searchParams: Record<string, string>) =>
    httpClient
        .get("orders", {
            searchParams,
        })
        .json<Collection<Order>>();

export type OrderbookRow = {
    level: number;
    buy_price: string;
    buy_amount: string;
    buy_cumulative_amount: string;
    sell_price: string;
    sell_amount: string;
    sell_cumulative_amount: string;
};
export const getOrderbook = (symbol: OrderSymbol) =>
    httpClient.get("orderbook", { searchParams: { symbol } }).json<{
        data: OrderbookRow[];
    }>();

export const cancelOrder = (orderId: number | string) =>
    httpClient.post(`orders/${orderId}/cancel`);

export type CreateOrderRequest = {
    symbol: OrderSymbol;
    side: number;
    price: number;
    amount: number;
};

export const createOrder = (json: CreateOrderRequest) =>
    httpClient.post("orders", { json }).json<{ data: Order }>();
