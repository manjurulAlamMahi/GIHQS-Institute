import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react'

export const baseApi = createApi({
  reducerPath: 'api',
  baseQuery: fetchBaseQuery({
    baseUrl: import.meta.env.VITE_API_BASE_URL,

    prepareHeaders: (headers, { getState }) => {
      const state = getState() as { auth: { token: string | null } }
      const token = state.auth.token
      if (token) headers.set('Authorization', `Bearer ${token}`)
      return headers
    },
  }),
  tagTypes: ['Courses', 'Auth'],
  endpoints: () => ({}),
})