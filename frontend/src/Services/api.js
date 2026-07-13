import axios from 'axios'

const api = axios.create({
  // Ajuste a porta/URL de acordo com o endereço onde seu backend (Laravel/PHP) está rodando localmente
  baseURL: 'http://localhost:8000/api', 
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Interceptor útil para injetar tokens de autenticação automaticamente no futuro
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

export default api