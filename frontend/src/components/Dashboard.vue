<template>
  <div class="dashboard">
    <h2>Dashboard</h2>

    <div class="dashboard__cards">
      <div class="card">
        <h3>Total Tickets</h3>
        <p>{{ stats.total }}</p>
      </div>
      <div class="card">
        <h3>By Status</h3>
        <ul>
          <li v-for="(count, status) in stats.by_status" :key="status">
            {{ status }}: {{ count }}
          </li>
        </ul>
      </div>
      <div class="card">
        <h3>By Category</h3>
        <ul>
          <li v-for="(count, category) in stats.by_category" :key="category">
            {{ category }}: {{ count }}
          </li>
        </ul>
      </div>
    </div>
      <h3>Tickets by Category and Status</h3>
    <div class="dashboard__cards">
      <div class="w-40">
          <canvas id="category" width="500" height="400"></canvas>
      </div>
      <div class="w-40">
          <canvas id="status" width="500" height="400"></canvas>
      </div>
    </div>
   
  </div>
</template>

<script>
import { Stats } from '../api/index.js'
import Chart from 'chart.js/auto'

export default {
  name: 'Dashboard',
  data() {
    return {
      stats: { total: 0, by_status: {}, by_category: {} },
      category: null,
      status: null,
    }
  },
  methods: {
    async fetchStats() {
      try {
        this.stats = await Stats.fetch()
        this.renderChart()
      } catch (e) {
        console.error('Error loading stats', e)
      }
    },
    renderChart() {
      if (this.category) this.category.destroy()
      const ctx_category = document.getElementById('category')
      this.category = new Chart(ctx_category, {
        type: 'bar',
        data: {
          labels: Object.keys(this.stats.by_category || {}),
          datasets: [{
            label: 'Tickets by Category',
            data: Object.values(this.stats.by_category || {})
          }]
        }
      })

        if (this.status) this.status.destroy()
      const ctx_status = document.getElementById('status')
      this.status = new Chart(ctx_status, {
        type: 'bar',
        data: {
          labels: Object.keys(this.stats.by_status || {}),
          datasets: [{
            label: 'Tickets by status',
            data: Object.values(this.stats.by_category || {})
          }]
        }
      })
    }
  },
  mounted() {
    this.fetchStats()
  }
}
</script>

<style>
.dashboard__cards {  display: flex;  gap: 20px;  margin-bottom: 20px;}
.card {  padding: 15px; border:1px solid #ccc; border-radius: 6px;}
@media (max-width: 768px) {  .dashboard__cards { flex-direction: column; }}
</style>
