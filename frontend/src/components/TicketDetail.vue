<template>
  <div v-if="ticket" class="ticket-detail">  
    <h2 class="ticket-number">Ticket #{{ ticket.id }}</h2>

    <p><strong>Subject:</strong> {{ ticket.subject }}</p>
    <p><strong>Body:</strong> {{ ticket.body }}</p>

    <div class="ticket-detail__form">
      <!-- Category -->
      <label>
        Category:
        <select v-model="form.category" @change="updateTicket">
          <option disabled value="">-- Select --</option>
          <option>billing</option>
          <option>technical</option>
          <option>account</option>
          <option>shipping</option>
          <option>sales</option>
          <option>other</option>
        </select>
      </label>

      <!-- Note -->
      <label>
        Internal Note:
        <textarea v-model="form.note" @blur="updateTicket" placeholder="Enter internal note"></textarea>
      </label>

      <!-- Status -->
      <label>
        Status:
        <select v-model="form.status" @change="updateTicket">
          <option>open</option>
          <option>in_progress</option>
          <option>resolved</option>
          <option>closed</option>
        </select>
      </label>
    </div>

    <!-- AI classification -->
    <div class="ticket-detail__ai">
      <h3>AI Classification</h3>
      <p><strong>Category (AI):</strong> {{ ticket.category }}</p>
      <p><strong>Confidence:</strong> {{ ticket.ai_confidence ?? '-' }}</p>
      <p><strong>Explanation:</strong> {{ ticket.ai_explanation ?? '-' }}</p>
    </div>

    <!-- Run classification -->
    <button class="btn" @click="classify" :disabled="loading">
      {{ loading ? 'Classifying...' : 'Run Classification' }}
    </button>
  </div>

  <p v-else>Loading...</p>
</template>

<script>
import { Tickets } from '../api'

export default {
  name: 'TicketDetail',
  props: ['id'],
  data() {
    return {
      ticket: null,
      form: { category: '', note: '', status: '' },
      loading: false
    }
  },
  methods: {
    async fetchTicket() {
      try {
       const res = await Tickets.get(this.id)
        this.ticket = res.data;
        this.form = {
          category: this.ticket.category || '',
          note: this.ticket.note || '',
          status: this.ticket.status || 'open'
        }
      } catch (e) {
        console.error('Error fetching ticket', e)
      }
    },
    async updateTicket() {
      try {
        const updated = await Tickets.update(this.id, this.form)
        this.ticket = updated.data
      } catch (e) {
        console.error('Error updating ticket', e)
      }
    },
    async classify() {
      this.loading = true
      try {
        await Tickets.classify(this.id)
        // refetch to show updated AI values
        setTimeout(this.fetchTicket, 2000) // small delay for job
      } catch (e) {
        console.error('Error classifying ticket', e)
      } finally {
        this.loading = false
      }
    }
  },
  mounted() {
    this.fetchTicket()
  }
}
</script>

<style>
.ticket-detail__form {
  margin: 15px 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.ticket-detail__ai {
  margin-top: 20px;
  padding: 10px;
  border:1px solid #ddd;
  border-radius:6px;
}
.btn { padding:6px 10px; border-radius:4px; cursor:pointer; margin-right:10px; border:1px solid #ccc; background:#f9f9f9;margin-top: 15px; }


select,input{ padding:6px 10px; border-radius:4px; border:1px solid #ccc; background:#fff; cursor:pointer; margin-left:8px; }
textarea{ padding:6px 10px; border-radius:4px; border:1px solid #ccc; background:#fff; cursor:pointer;}
textarea { width: 100%; min-height: 80px; }
@media (max-width: 600px) {
.ticket-number{font-size:1rem; margin-bottom:10px;}
}
</style>
