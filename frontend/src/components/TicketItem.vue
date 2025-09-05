<template>
  <div class="ticket-item">
    <div class="ticket-item__main" @click="$emit('open', ticket)" role="button">
      <h3 class="ticket-item__subject">{{ ticket.subject }}</h3>
      <p class="ticket-item__body">{{ truncatedBody }}</p>
    </div>

    <div class="ticket-item__meta">
      <span class="badge" v-if="ticket.category">{{ ticket.category }}</span>

      <span class="ticket-item__confidence" v-if="confidence !== null" :title="explanationTitle">
        {{ confidence }} 
      </span>

      <button class="btn" @click.stop="$emit('classify', ticket)" :disabled="loading">
        <span v-if="loading">⏳</span>
        <span v-else>Classify</span>
      </button>

      <span v-if="ticket.note" class="ticket-item__note-indicator" :title="ticket.note">📝</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TicketItem',
  props: {
    ticket: { type: Object, required: true },
    loading: { type: Boolean, default: false }
  },
  computed: {
    truncatedBody() {
      if (!this.ticket.body) return ''
      return this.ticket.body.length > 140 ? this.ticket.body.slice(0, 140) + '…' : this.ticket.body
    },
    confidence() {
      const c = this.ticket.ai_confidence
      if (c === null || c === undefined) return null
      // backend stores 0-100; show 0-1 with 2 decimals (as requested)
      return (c / 100).toFixed(2)
    },
    explanationTitle() {
      return this.ticket.ai_explanation || 'No explanation'
    }
  }
}
</script>

<style scoped>
.ticket-item { display:flex; justify-content:space-between; gap:12px; align-items:flex-start; }
.ticket-item__main { flex:1; cursor:pointer; }
.ticket-item__subject { margin:0; font-size:1rem; }
.ticket-item__body { margin:4px 0 0; color:#444; font-size:0.9rem; }
.ticket-item__meta { display:flex; gap:8px; align-items:center; margin-left:12px; min-width:160px; justify-content:flex-end; }
.badge { background:#eef; padding:4px 8px; border-radius:12px; font-size:0.8rem; }
.ticket-item__note-indicator { font-size:1rem; cursor:pointer; }
.ticket-item__confidence { font-weight:600; font-size:0.9rem; }
.btn { padding:6px 10px; border-radius:4px; cursor:pointer; margin-right:10px; border:1px solid #ccc; background:#f9f9f9;margin-top: 0; }
.app.dark .ticket-item__body { color:#fff; }
.app.dark .badge { color:#000; }

@media (max-width: 768px) { 
  .ticket-item{flex-direction:column;}
  .ticket-item__meta{margin-left:0;}
}
</style>
