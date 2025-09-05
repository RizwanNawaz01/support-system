import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue' 
// Pages
import TicketList from './components/TicketList.vue'
import TicketDetail from './components/TicketDetail.vue'
import Dashboard from './components/Dashboard.vue'

const routes = [
  { path: '/', redirect: '/tickets' },
  { path: '/tickets', component: TicketList },
  { path: '/tickets/:id', component: TicketDetail, props: true },
  { path: '/dashboard', component: Dashboard }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

const app = createApp(App)
app.use(router)
app.mount('#app')
