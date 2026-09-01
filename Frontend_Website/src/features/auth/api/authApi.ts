import { baseApi } from '@/lib/baseApi'
import type { 
  LoginRequest, LoginResponse, 
  RegisterRequest, RegisterResponse, 
  RegisterVerifyOtpRequest, RegisterVerifyOtpResponse,
  ProfileResponse, LogoutResponse,
  SendOtpRequest, SendOtpResponse,
  VerifyOtpRequest, VerifyOtpResponse,
  ResetPasswordRequest, ResetPasswordResponse,
  ChangePasswordRequest, ChangePasswordResponse
} from '@/types/auth.types'

export const authApi = baseApi.injectEndpoints({
  endpoints: (builder) => ({

    login: builder.mutation<LoginResponse, LoginRequest>({
      query: (body) => ({ url: '/login', method: 'POST', body }),
    }),

    register: builder.mutation<RegisterResponse, RegisterRequest>({
      query: (body) => ({ url: '/register', method: 'POST', body }),
    }),

    registerVerifyOtp: builder.mutation<RegisterVerifyOtpResponse, RegisterVerifyOtpRequest>({
      query: (body) => ({ url: '/register/verify-otp', method: 'POST', body }),
    }),

    logout: builder.mutation<LogoutResponse, void>({
      query: () => ({ url: '/logout', method: 'POST' }),
    }),

    sendOtp: builder.mutation<SendOtpResponse, SendOtpRequest>({
      query: (body) => ({ url: '/password/send-otp', method: 'POST', body }),
    }),

    verifyOtp: builder.mutation<VerifyOtpResponse, VerifyOtpRequest>({
      query: (body) => ({ url: '/password/verify-otp', method: 'POST', body }),
    }),

    resetPassword: builder.mutation<ResetPasswordResponse, ResetPasswordRequest>({
      query: (body) => ({ url: '/password/reset', method: 'POST', body }),
    }),

    getProfileInfo: builder.query<ProfileResponse, void>({
      query: () => '/profile-info',
      providesTags: ['Auth'],
    }),

    updateProfile: builder.mutation<ProfileResponse, FormData>({
      query: (body) => ({ 
        url: '/profile-update', 
        method: 'POST', 
        body 
      }),
      invalidatesTags: ['Auth'],
    }),

    changePassword: builder.mutation<ChangePasswordResponse, ChangePasswordRequest>({
      query: (body) => ({ 
        url: '/profile-change-password', 
        method: 'POST', 
        body 
      }),
    }),

  }),
})

export const { 
  useLoginMutation, 
  useRegisterMutation, 
  useRegisterVerifyOtpMutation,
  useLogoutMutation, 
  useGetProfileInfoQuery,
  useUpdateProfileMutation,
  useChangePasswordMutation,
  useSendOtpMutation,
  useVerifyOtpMutation,
  useResetPasswordMutation
} = authApi
