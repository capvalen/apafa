import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import VueAxios from 'vue-axios'
import axios from "axios";

const app = createApp(App)
app.config.globalProperties.servidor = 'http://localhost/apafa/api/';

//createApp(App).use(router).mount('#app')

app
.use(router)
.use(VueAxios, axios)
.mount('#app')