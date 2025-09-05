<template>
  <div id="app" :class="['app', { dark: isDark }]"> 
    <header class="app-header">
      <h1>Helpdesk</h1>
      <nav>
        <router-link to="/tickets">Tickets</router-link>
        <router-link to="/dashboard">Dashboard</router-link>
         <div > 
          <button @click="toggleDarkMode">
            Toggle Dark Mode
          </button>
        </div>
      </nav>
    </header>

    <main>
      <router-view />
    </main>

    <footer class="app-footer">
      <p>
        &copy; 2025 Helpdesk App
      </p>
    </footer>
  </div>
</template>

<script>
export default { name: 'App',
  data() {
    return {
      isDark: false
    }
  },
methods:{
  toggleDarkMode(){ 
      this.isDark = !this.isDark
  }

 }
}
</script>

<style>
body { font-family: sans-serif; margin:0; }
.app-header {  display:flex; justify-content:space-between; align-items:center;  padding:10px 20px; background:#222; color:#fff;}
.app-header a { color:#fff; margin-left:15px; text-decoration:none; }
main { padding:20px; }
.app-footer { text-align:center; color:#fff; font-size:0.9rem; padding:10px 0; border-top:1px solid #eee; margin-top:20px;    background: #222; }
 .app {   flex-direction: column;  align-items: center;  justify-content: center;  background: white;  color: black;  transition: all 0.3s ease;}
.app.dark {  background: #121212;  color: white;}
.app.dark  .app-header {  background: #fff;}
.app.dark  .app-header a {  color: #222; }
.app.dark  .app-footer {  background: #fff;  color: #222;}
.app.dark h1{  color: #222;}
button {  margin-top: 20px;  padding: 10px 20px;  border: none;  border-radius: 8px;  cursor: pointer;}
</style>
