import { configureStore } from '@reduxjs/toolkit'
import { baseApi } from '@/lib/baseApi'
import rootReducer from './rootReducer'

export const store = configureStore({
  reducer: rootReducer,
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware().concat(baseApi.middleware),
})

export type AppDispatch = typeof store.dispatch