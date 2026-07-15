import axios from 'axios'
export const API_BASE_URL = import.meta.env.VITE_API_BASE_URL
const api = axios.create({
  baseURL: `${API_BASE_URL}/api/v1/`,
  timeout: 10000,
  withCredentials: true, // Permite o envio de cookies para autenticação baseada em sessão
  withXSRFToken: true, // Habilita a proteção CSRF
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})
export async function initializeCSRF() {
  return axios.get(`${API_BASE_URL}/sanctum/csrf-cookie`, {
    withCredentials: true,
    withXSRFToken: true
  })
}
/*api.interceptors.request.use(
  (config) => {
    //const token = localStorage.getItem('token')
    //if (token) {
     // config.headers.Authorization = `Bearer ${token}`
   // }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)*/

export default api