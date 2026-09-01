import { useForm } from "react-hook-form"
import { zodResolver } from "@hookform/resolvers/zod"
import { Link, useLocation, useNavigate } from "react-router"
import { CheckCircle2, ChevronLeft, KeyRound, Mail } from "lucide-react"
import { toast } from "sonner"

import { useAppDispatch } from "@/app/hooks"
import { AuthLogo } from "@/components/shared/AuthLogo"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { useRegisterVerifyOtpMutation } from "@/features/auth/api/authApi"
import { setCredentials } from "@/features/auth/store/authSlice"
import { ROUTES } from "@/routes/routes.constants"
import { verifyOtpSchema, type VerifyOtpFormValues } from "@/types/auth.types"

type VerifyEmailLocationState = {
  email?: string
  from?: string
  rememberMe?: boolean
}

type ApiValidationErrors = Record<string, string[]>
type VerifyEmailErrorResponse = {
  data?: {
    status?: boolean
    success?: boolean
    data?: ApiValidationErrors | never[]
    message?: string
  }
  error?: string
}

export default function VerifyEmailPage() {
  const dispatch = useAppDispatch()
  const navigate = useNavigate()
  const location = useLocation()
  const state = (location.state || {}) as VerifyEmailLocationState
  const email = state.email || ""
  const from = state.from || ROUTES.HOME
  const rememberMe = Boolean(state.rememberMe)
  const [verifyRegistrationOtp, { isLoading }] = useRegisterVerifyOtpMutation()

  const {
    register,
    handleSubmit,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<VerifyOtpFormValues>({
    resolver: zodResolver(verifyOtpSchema),
  })

  const onSubmit = async (data: VerifyOtpFormValues) => {
    if (!email) {
      setError("root", {
        type: "server",
        message: "Please create an account again to receive a verification code.",
      })
      return
    }

    try {
      const response = await verifyRegistrationOtp({
        email,
        otp: parseInt(data.otp, 10),
      }).unwrap()

      if (response.success === false) {
        throw { data: response }
      }

      if (response.success || response.code === 200) {
        toast.success(response.message || "Email verified successfully")
        dispatch(
          setCredentials({
            token: response.data.token,
            user: {
              id: String(response.data.id),
              name: response.data.full_name || `${response.data.first_name} ${response.data.last_name}`,
              email: response.data.email,
              avatar: response.data.avatar,
            },
            rememberMe,
          })
        )
        navigate(from, { replace: true })
      }
    } catch (err) {
      const errorResponse = err as VerifyEmailErrorResponse

      if (errorResponse?.data?.status === false || errorResponse?.data?.success === false) {
        if (errorResponse.data.message) {
          setError("root", { type: "server", message: errorResponse.data.message })
        }

        const validationErrors = errorResponse.data.data
        if (validationErrors && typeof validationErrors === "object" && !Array.isArray(validationErrors)) {
          Object.keys(validationErrors).forEach((key) => {
            const message = validationErrors[key][0]
            if (key === "otp") setError("otp", { type: "server", message })
            if (key === "email") setError("root", { type: "server", message })
          })
        }
      } else {
        setError("root", {
          type: "server",
          message: errorResponse?.data?.message || errorResponse?.error || "An unexpected error occurred. Please try again.",
        })
      }
    }
  }

  return (
    <div className="min-h-screen container mx-auto bg-[#F7FAF9] flex flex-col pt-8 sm:pt-16 items-center px-4 relative">
      <Link
        to={ROUTES.SIGNUP}
        className="absolute top-6 left-6 md:top-10 md:left-10 flex items-center text-sm font-semibold text-[#1B3C32] hover:opacity-70 transition-opacity"
      >
        <ChevronLeft className="w-4 h-4 mr-1" />
        Back to Signup
      </Link>

      <AuthLogo />

      <div className="w-full max-w-120 bg-white rounded-[24px] shadow-sm shadow-[#1B3C32]/3 p-8 md:p-10 border border-[#E5EDE9] mb-12">
        <div className="text-center mb-8">
          <div className="w-14 h-14 rounded-full bg-[#1A5C4A]/10 text-[#1A5C4A] flex items-center justify-center mx-auto mb-5">
            <Mail className="w-7 h-7" />
          </div>
          <h1 className="text-2xl font-bold text-[#111827] mb-2 font-['Outfit']">Verify your email</h1>
          <p className="text-[13px] text-[#6B7280] leading-relaxed">
            We sent a 6-digit code to <span className="font-semibold text-[#111827]">{email || "your email"}</span>
          </p>
        </div>

        {errors.root && (
          <div className="mb-6 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600 text-center font-medium">
            {errors.root.message}
          </div>
        )}

        <form onSubmit={handleSubmit(onSubmit)} className="space-y-5">
          <div className="space-y-2 relative">
            <Label htmlFor="otp" className="text-xs font-semibold text-[#374151]">6-Digit Code</Label>
            <div className="relative">
              <KeyRound className="absolute left-3.5 top-1/2 -translate-y-1/2 h-4.5 w-4.5 text-[#9CA3AF] pointer-events-none" />
              <Input
                id="otp"
                type="text"
                inputMode="numeric"
                maxLength={6}
                placeholder="Enter 6-digit OTP"
                className="pl-11 h-12 bg-[#F3F4F6] tracking-[0.2em] border-transparent focus-visible:ring-[#1E5F4C]/20 focus-visible:bg-white transition-colors rounded-xl text-sm font-medium"
                {...register("otp")}
              />
            </div>
            {errors.otp && <p className="text-xs text-red-500 mt-1">{errors.otp.message}</p>}
          </div>

          <Button
            type="submit"
            disabled={isSubmitting || isLoading}
            className="w-full h-12 bg-[#1A5C4A] hover:bg-[#154637] text-white rounded-xl font-medium text-[15px] transition-colors shadow-sm group"
          >
            {isSubmitting || isLoading ? "Verifying..." : "Verify Email"} <CheckCircle2 className="w-4 h-4 ml-2 group-hover:w-5 group-hover:scale-110 transition-transform" />
          </Button>
        </form>

        <div className="mt-8 text-center text-[13px] text-[#4B5563] font-medium">
          Wrong email?{" "}
          <Link to={ROUTES.SIGNUP} className="text-primary font-bold underline underline-offset-2">
            Create account again
          </Link>
        </div>
      </div>
    </div>
  )
}
