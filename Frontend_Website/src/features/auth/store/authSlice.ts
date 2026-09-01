import type { AuthState, User } from '@/types/auth.types';
import { createSlice } from '@reduxjs/toolkit'
import type { PayloadAction } from '@reduxjs/toolkit'

const sessionToken = sessionStorage.getItem('token')
const sessionUser = sessionStorage.getItem('user')

// Auto-migrate current session to localStorage so new tabs stay logged in
if (sessionToken && !localStorage.getItem('token')) {
  localStorage.setItem('token', sessionToken)
  if (sessionUser) localStorage.setItem('user', sessionUser)
}

const savedToken = localStorage.getItem('token')
const savedUser = localStorage.getItem('user')

const initialState: AuthState = {
  token: savedToken,
  user: savedUser ? JSON.parse(savedUser) : null,
}

const authSlice = createSlice({
  name: 'auth',
  initialState,
  reducers: {
    setCredentials: (state, { payload }: PayloadAction<{ token: string; user: User; rememberMe?: boolean }>) => {
      state.token = payload.token
      state.user  = payload.user

      localStorage.removeItem('token')
      localStorage.removeItem('user')
      sessionStorage.removeItem('token')
      sessionStorage.removeItem('user')

      const storage = payload.rememberMe === false ? sessionStorage : localStorage
      storage.setItem('token', payload.token)
      storage.setItem('user', JSON.stringify(payload.user))
    },
    updateUser: (state, { payload }: PayloadAction<User>) => {
      state.user = payload
      const storage = localStorage.getItem('token') ? localStorage : sessionStorage
      storage.setItem('user', JSON.stringify(payload))
    },
    logout: (state) => {
      state.token = null
      state.user  = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      sessionStorage.removeItem('token')
      sessionStorage.removeItem('user')
    },
  },
})

export const { setCredentials, updateUser, logout } = authSlice.actions
export default authSlice.reducer
