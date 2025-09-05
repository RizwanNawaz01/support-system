import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000/api', 
  headers: { 'Content-Type': 'application/json' }
})

export const Tickets = {
  async list(params = {}) {
    const res = await api.get('/tickets', { params })
    return res.data
  },
  async get(id) {
    const res = await api.get(`/tickets/${id}`)
    return res.data
  },
  async create(payload) {
    const res = await api.post('/tickets', payload)
    return res.data
  },
  async update(id, payload) {
    const res = await api.patch(`/tickets/${id}`, payload)
    return res.data
  },
  async classify(id) {
    const res = await api.post(`/tickets/${id}/classify`)
    return res.data
  }
}

export const Stats = {
  async fetch() {
    const res = await api.get('/stats')
    return res.data
  }
}

export default api
