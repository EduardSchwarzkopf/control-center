require("./bootstrap");

require("alpinejs");

import { createApp } from "vue";
import router from "./router";

createApp({}).use(router).mount("#app");
