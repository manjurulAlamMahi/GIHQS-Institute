import { combineReducers } from '@reduxjs/toolkit'
import { baseApi } from '@/lib/baseApi'
import authReducer from '@/features/auth/store/authSlice'

const rootReducer = combineReducers({
  auth: authReducer,
  [baseApi.reducerPath]: baseApi.reducer,
})

export type RootState = ReturnType<typeof rootReducer>
export default rootReducer