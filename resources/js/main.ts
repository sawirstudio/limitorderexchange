import { configureEcho } from "@laravel/echo-vue";
import { createApp } from "vue";
import App from "./App.vue";
import "../css/main.css";
import "vue-sonner/style.css";
import { VueQueryPlugin } from "@tanstack/vue-query";

configureEcho({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    // wsHost: import.meta.env.VITE_PUSHER_HOST,
    wsPort: import.meta.env.VITE_PUSHER_PORT,
    wssPort: import.meta.env.VITE_PUSHER_PORT,
    enabledTransports: ["ws", "wss"],
});

createApp(App).use(VueQueryPlugin).mount("#app");
