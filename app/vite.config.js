import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
	allowedHosts: true,
    port: 3000,
    proxy: {
      '/api': {
        target: 'http://host.docker.internal:8000',
        changeOrigin: true,
        secure: false
      },
      '/login': {
        target: 'http://host.docker.internal:8000',
        changeOrigin: true,
        secure: false
      },
      '/logout': {
        target: 'http://host.docker.internal:8000',
        changeOrigin: true,
        secure: false
      }
    }
  }
})
