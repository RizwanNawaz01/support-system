<template>
  <div class="modal-backdrop" @click.self="close">
    <div class="modal">
      <h2>New Ticket</h2>

      <form @submit.prevent="submit">
        <label>
          Subject
          <input v-model="subject" @input="validateField('subject')" />
          <div class="error" v-if="errors.subject">{{ errors.subject }}</div>
        </label>

        <label>
          Body
          <textarea v-model="body" @input="validateField('body')" rows="6"></textarea>
          <div class="error" v-if="errors.body">{{ errors.body }}</div>
        </label>

        <div class="modal__actions">
          <button type="button" class="btn" @click="close">Cancel</button>
          <button type="submit" class="btn" :disabled="submitting">
            <span v-if="submitting">Creating…</span>
            <span v-else>Create</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Tickets } from '../api/index.js'

export default {
  name: 'NewTicketModal',
  data() {
    return {
      subject: '',
      body: '',
      errors: {},
      submitting: false
    }
  },
  methods: {
    validateField(field) {
      this.errors[field] = ''
      if (field === 'subject' && !this.subject.trim()) this.errors.subject = 'Subject is required'
      if (field === 'body' && !this.body.trim()) this.errors.body = 'Body is required'
    },
    async submit() {
      this.validateField('subject')
      this.validateField('body')
      if (this.errors.subject || this.errors.body) return

      this.submitting = true
      try {
        const payload = { subject: this.subject.trim(), body: this.body.trim(), status: 'open' }
        const created = await Tickets.create(payload)
        // created is the Ticket resource returned from API
        this.$emit('created', created)
        this.close()
      } catch (err) {
        console.error('Create ticket failed', err)
        alert('Failed to create ticket')
      } finally {
        this.submitting = false
      }
    },
    close() {
      this.$emit('close')
    }
  }
}
</script>

<style scoped>
.modal-backdrop { position:fixed; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.3); }
.modal { background:#fff; padding:20px; border-radius:8px; width:540px; max-width:95%; }
label { display:block; margin-bottom:10px; }
input, textarea { width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; }
.error { color:#b00; font-size:0.85rem; margin-top:4px; }
.modal__actions { display:flex; justify-content:flex-end; gap:8px; margin-top:12px; }
</style>
