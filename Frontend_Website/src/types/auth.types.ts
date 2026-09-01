import * as z from "zod"

export interface User {
  id: string
  name: string
  email: string
  avatar?: string | null
}

export interface AuthState {
  token: string | null
  user: User | null
}

export interface LoginRequest {
  email: string
  password: string
}

export interface RegisterRequest {
  first_name: string
  last_name: string
  country: string
  email: string
  password: string
}

export interface ApiUser {
  id: number
  first_name: string
  last_name: string
  full_name: string
  country: string
  username: string
  email: string
  phone: string
  avatar: string
  token: string
  role: string
}

export interface LoginResponse {
  success: boolean
  message: string
  data: ApiUser
  code: number
}

export interface ProfileResponse {
  success: boolean
  message: string
  data: ApiUser & {
    address?: string | null
    city?: string | null
    zip?: string | null
    state?: string | null
    designation?: string | null
  }
  code: number
}

export interface LogoutResponse {
  success: boolean
  message: string
  data: {
    full_name: string
  }
  code: number
}

export interface SendOtpRequest {
  email: string
}

export interface SendOtpResponse {
  success: boolean
  message: string
  data: {
    email: string
    otp: number
    otp_verified: boolean
    otp_attempts: number
    otp_expired_at: string
  }
  code: number
}

export interface VerifyOtpRequest {
  email: string
  otp: number
}

export interface VerifyOtpResponse {
  success: boolean
  message: string
  data: {
    email: string
    password_reset_token: string
  }
  code: number
}

export interface ResetPasswordRequest {
  email: string
  password_reset_token: string
  password?: string
  password_confirmation?: string
}

export interface ResetPasswordResponse {
  success: boolean
  message: string
  data: ApiUser
  code: number
}

export interface ChangePasswordRequest {
  old_password: string
  new_password: string
  new_password_confirmation: string
}

export interface ChangePasswordResponse {
  success: boolean
  message: string
  data: any[]
  code: number
}

export interface RegisterResponse {
  success: boolean
  message: string
  data: {
    email: string
    otp: number
    otp_verified: boolean
    otp_expired_at: string
  }
  code: number
}

export interface RegisterVerifyOtpRequest {
  email: string
  otp: number
}

export interface RegisterVerifyOtpResponse {
  success: boolean
  message: string
  data: ApiUser
  code: number
}

export const signupSchema = z.object({
  firstName: z.string().min(1, { message: "First name is required" }),
  lastName: z.string().min(1, { message: "Last name is required" }),
  country: z.string().optional(),
  email: z.email({ message: "Please enter a valid email address" }),
  password: z.string().min(6, { message: "Password must be at least 6 characters long" }),
  rememberMe: z.boolean().default(false).optional(),
})

export type SignupFormValues = z.infer<typeof signupSchema>

export const loginSchema = z.object({
  email: z.email({ message: "Please enter a valid email address" }),
  password: z.string().min(6, { message: "Password must be at least 6 characters long" }),
  rememberMe: z.boolean().default(false).optional(),
})

export type LoginFormValues = z.infer<typeof loginSchema>

export const forgotPasswordSchema = z.object({
  email: z.email({ message: "Please enter a valid email address" }),
})

export type ForgotPasswordFormValues = z.infer<typeof forgotPasswordSchema>

export const verifyOtpSchema = z.object({
  otp: z.string().min(6, { message: "OTP must be at least 6 digits long" }),
})

export type VerifyOtpFormValues = z.infer<typeof verifyOtpSchema>

export const resetPasswordSchema = z.object({
  password: z.string().min(6, { message: "Password must be at least 6 characters" }),
  password_confirmation: z.string().min(6, { message: "Password confirmation is required" })
}).refine((data) => data.password === data.password_confirmation, {
  message: "Passwords do not match",
  path: ["password_confirmation"]
})

export type ResetPasswordFormValues = z.infer<typeof resetPasswordSchema>
