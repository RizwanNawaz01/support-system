<template>
  <div class="ticket-list">
    <div class="ticket-list__controls">
      <div class="ticket-list__left">
        <button class="btn" @click="showNewModal = true">New Ticket</button>
        <button class="btn" @click="exportCsv" :disabled="!tickets.length">Export CSV</button>
      </div>

      <div class="ticket-list__right">
        <input
          class="ticket-list__search"
          v-model="search"
          @input="debouncedLoad"
          placeholder="Search subject or body..."
        />

        <select v-model="status" @change="load" aria-label="Filter by status">
          <option value="">All statuses</option>
          <option value="open">Open</option>
          <option value="in_progress">In Progress</option>
          <option value="resolved">Resolved</option>
          <option value="closed">Closed</option>
        </select>

        <select v-model="category" @change="load" aria-label="Filter by category">
          <option value="">All categories</option>
          <option value="billing">Billing</option>
          <option value="technical">Technical</option>
          <option value="account">Account</option>
          <option value="shipping">Shipping</option>
          <option value="sales">Sales</option>
          <option value="other">Other</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="ticket-list__loading">Loading…</div>

    <ul class="ticket-list__items">
      <li
        v-for="t in tickets"
        :key="t.id"
        class="ticket-list__item"
        :class="{ 'ticket-list__item--active': selectedId === t.id }"
      >
        <TicketItem
          :ticket="t"
          :loading="isClassifying[t.id]"
          @classify="onClassify"
          @open="openTicket"
        />
      </li>
    </ul>

    <div class="ticket-list__pagination" v-if="meta">
      <button class="btn" :disabled="meta.current_page <= 1" @click="load(meta.current_page - 1)">
        Prev
      </button>

      <span>Page {{ meta.current_page }} / {{ meta.last_page }}</span>

      <button class="btn" :disabled="meta.current_page >= meta.last_page" @click="load(meta.current_page + 1)">
        Next
      </button>

      <select v-model.number="perPage" @change="load(1)">
        <option :value="10">10</option>
        <option :value="15">15</option>
        <option :value="25">25</option>
      </select>
    </div>

    <NewTicketModal v-if="showNewModal" @created="onCreated" @close="showNewModal = false" />
  </div>
</template>

<script>
import { Tickets } from '../api/index.js'
import TicketItem from './TicketItem.vue'
import NewTicketModal from './NewTicketModal.vue'
import { exportToCsv } from '../utils/csv.js'

export default {
  name: 'TicketList',
  components: { TicketItem, NewTicketModal },
  data() {
    return {
      tickets: [],
      meta: null,
      page: 1,
      perPage: 15,
      search: '',
      status: '',
      category: '',
      loading: false,
      isClassifying: {}, // { [id]: boolean }
      showNewModal: false,
      selectedId: null,
      debounceTimer: null
    }
  },
  mounted() {
    this.load(1)
  },
  methods: {
    debouncedLoad() {
      clearTimeout(this.debounceTimer)
      this.debounceTimer = setTimeout(() => this.load(1), 400)
    },

    async load(page = 1) {
      this.loading = true
      this.page = page
      try {
        const params = {
          page,
          per_page: this.perPage,
        }
        if (this.search) params.search = this.search
        if (this.status) params.status = this.status
        if (this.category) params.category = this.category

        const res = await Tickets.list(params)
        // Laravel pagination: { data: [...], current_page, last_page, ... }
        this.tickets = res.data || []
        this.meta = {
          current_page: res.current_page || 1,
          last_page: res.last_page || 1
        }
      } catch (err) {
        console.error('Failed to load tickets', err)
        alert('Failed to load tickets')
      } finally {
        this.loading = false
      }
    },

async onClassify(ticket) {
  // set spinner
  this.isClassifying[ticket.id] = true

  try {
    await Tickets.classify(ticket.id)
    // poll for up to 20s for ai_confidence to appear (job results)
    const updated = await this.pollForClassification(ticket.id, 20000, 1000)
    if (updated) {
      const idx = this.tickets.findIndex(t => t.id === updated.id)
      if (idx !== -1) this.tickets.splice(idx, 1, updated)
    } else {
      this.load(this.page) // fallback reload
    }
  } catch (err) {
    console.error(err)
    alert('Failed to queue classification')
  } finally {
    delete this.isClassifying[ticket.id]
  }
},


    async pollForClassification(id, timeout = 15000, interval = 1000) {
      const start = Date.now()
      while (Date.now() - start < timeout) {
        try {
          const updated = await Tickets.get(id)
          // treat non-null confidence or explanation as done
          if (updated.ai_confidence !== null && updated.ai_confidence !== undefined) {
            return updated
          }
        } catch (err) {
          // ignore transient errors
        }
        await new Promise(r => setTimeout(r, interval))
      }
      return null
    },

    openTicket(ticket) {
      this.selectedId = ticket.id
      this.$router.push({ path: `/tickets/${ticket.id}` })
    },

    async onCreated(newTicket) {
      // Close modal handled by modal itself via @created, push ticket to top
      this.showNewModal = false
      this.tickets.unshift(newTicket)
    },

    exportCsv() {
      const rows = this.tickets.map(t => ({
        id: t.id,
        subject: t.subject,
        category: t.category ?? '',
        status: t.status ?? '',
        confidence: t.ai_confidence ?? '',
        note: t.note ?? ''
      }))
      exportToCsv('tickets.csv', rows)
    }
  }
}
</script>

<style scoped>
.ticket-list__controls {  display:flex;  justify-content:space-between;  gap:12px;  margin-bottom:12px;  align-items:center;}
.ticket-list__items { list-style:none; padding:0; margin:0; display:grid; gap:10px;  grid-template-columns: auto auto auto; margin-bottom:12px; }
.ticket-list__item { border:1px solid #eee; padding:12px; border-radius:6px; background:#fff; }
.ticket-list__item--active { box-shadow: 0 0 0 3px rgba(0,120,255,0.06); }
.ticket-list__loading { padding:20px; text-align:center; }
.ticket-list__search { padding:6px 10px; margin-right:8px; }
.btn { padding:6px 10px; border-radius:4px; cursor:pointer; margin-right:10px; border:1px solid #ccc; background:#f9f9f9; }
select,input{ padding:6px 10px; border-radius:4px; border:1px solid #ccc; background:#fff; cursor:pointer; margin-left:8px; }
.app.dark .ticket-list__item { background: #1e1e1e; border-color: #333; }
@media (max-width: 768px) {
.ticket-list__items { grid-template-columns: auto; }
.ticket-list__controls {flex-direction:column; align-items:flex-start; }
.ticket-list__search,select { margin-bottom: 10px;margin-left:0 }    
}


</style>
