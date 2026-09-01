import { useState } from "react"
import { useForm } from "react-hook-form"
import { zodResolver } from "@hookform/resolvers/zod"
import { Link, useNavigate } from "react-router"
import { Mail, ChevronLeft, ArrowRight, CheckCircle2, KeyRound, Lock, Eye, EyeOff } from "lucide-react"

import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Button } from "@/components/ui/button"
import { AuthLogo } from "@/components/shared/AuthLogo"
import { ROUTES } from "@/routes/routes.constants"
import { useSendOtpMutation, useVerifyOtpMutation, useResetPasswordMutation } from "@/features/auth/api/authApi"
import { 
  forgotPasswordSchema, type ForgotPasswordFormValues,
  verifyOtpSchema, type VerifyOtpFormValues,
  resetPasswordSchema, type ResetPasswordFormValues
} from "@/types/auth.types"
import { toast } from "sonner"
import { useAppDispatch } from "@/app/hooks"
import { setCredentials } from "@/features/auth/store/authSlice"

export default function ForgotPasswordPage() {
  const [step, setStep] = useState<"email" | "otp" | "password" | "success">("email")
  const [email, setEmail] = useState("")
  const [resetToken, setResetToken] = useState("")
  const [showPassword, setShowPassword] = useState(false)
  const [showConfirmPassword, setShowConfirmPassword] = useState(false)
  
  const navigate = useNavigate()
  const dispatch = useAppDispatch()
  const [sendOtp, { isLoading: isSending }] = useSendOtpMutation()
  const [verifyOtp, { isLoading: isVerifying }] = useVerifyOtpMutation()
  const [resetPassword, { isLoading: isResetting }] = useResetPasswordMutation()
  
  const emailForm = useForm<ForgotPasswordFormValues>({
    resolver: zodResolver(forgotPasswordSchema)
  })

  const otpForm = useForm<VerifyOtpFormValues>({
    resolver: zodResolver(verifyOtpSchema)
  })

  const passwordForm = useForm<ResetPasswordFormValues>({
    resolver: zodResolver(resetPasswordSchema)
  })

  const onEmailSubmit = async (data: ForgotPasswordFormValues) => {
    try {
      const response = await sendOtp({ email: data.email }).unwrap() as any;
      if (response.status === false || response.success === false) throw { data: response };
      
      setEmail(data.email);
      setStep("otp");
      toast.success(response.message || "OTP sent successfully.");
    } catch (err) {
      const errorResponse = err as { data?: { status?: boolean, data?: Record<string, string[]> | never[], message?: string }, error?: string };
      if (errorResponse?.data?.status === false) {
        if (errorResponse.data.message) {
          emailForm.setError("root", { type: 'server', message: errorResponse.data.message });
        }
        const validationErrors = errorResponse.data.data;
        if (validationErrors && typeof validationErrors === 'object' && !Array.isArray(validationErrors)) {
          Object.keys(validationErrors).forEach((key) => {
            const message = (validationErrors as Record<string, string[]>)[key][0];
            if (key === 'email') emailForm.setError("email", { type: 'server', message });
          });
        }
      } else {
        emailForm.setError("root", { type: 'server', message: errorResponse?.data?.message || errorResponse?.error || "An unexpected error occurred." });
      }
    }
  }

  const onOtpSubmit = async (data: VerifyOtpFormValues) => {
    try {
      const response = await verifyOtp({ 
        email, 
        otp: parseInt(data.otp, 10) 
      }).unwrap() as any;
      
      if (response.status === false || response.success === false) throw { data: response };
      
      setResetToken(response.data?.password_reset_token || "");
      setStep("password");
      toast.success(response.message || "OTP verified successfully");
    } catch (err) {
      const errorResponse = err as { data?: { status?: boolean, data?: Record<string, string[]> | never[], message?: string }, error?: string };
      if (errorResponse?.data?.status === false) {
        if (errorResponse.data.message) {
          otpForm.setError("root", { type: 'server', message: errorResponse.data.message });
        }
        const validationErrors = errorResponse.data.data;
        if (validationErrors && typeof validationErrors === 'object' && !Array.isArray(validationErrors)) {
          Object.keys(validationErrors).forEach((key) => {
            const message = (validationErrors as Record<string, string[]>)[key][0];
            if (key === 'otp') otpForm.setError("otp", { type: 'server', message });
          });
        }
      } else {
        otpForm.setError("root", { type: 'server', message: errorResponse?.data?.message || errorResponse?.error || "An unexpected error occurred." });
      }
    }
  }

  const onPasswordSubmit = async (data: ResetPasswordFormValues) => {
    try {
      const response = await resetPassword({ 
        email, 
        password_reset_token: resetToken,
        password: data.password,
        password_confirmation: data.password_confirmation
      }).unwrap() as any;
      
      if (response.status === false || response.success === false) throw { data: response };
      
      // Reset & Login successful
      if (response.data && response.data.token) {
        dispatch(setCredentials({ 
          token: response.data.token, 
          user: {
            id: String(response.data.id),
            name: response.data.full_name || `${response.data.first_name} ${response.data.last_name}`,
            email: response.data.email,
            avatar: response.data.avatar,
          }
        }));
      }

      setStep("success");
      toast.success(response.message || "Password reset successful");
      // Optional: automatically redirect to dashboard after a short delay
      setTimeout(() => navigate(ROUTES.DASHBOARD), 2000);
    } catch (err) {
      const errorResponse = err as { data?: { status?: boolean, data?: Record<string, string[]> | never[], message?: string }, error?: string };
      if (errorResponse?.data?.status === false) {
        if (errorResponse.data.message) {
          passwordForm.setError("root", { type: 'server', message: errorResponse.data.message });
        }
        const validationErrors = errorResponse.data.data;
        if (validationErrors && typeof validationErrors === 'object' && !Array.isArray(validationErrors)) {
          Object.keys(validationErrors).forEach((key) => {
            const message = (validationErrors as Record<string, string[]>)[key][0];
            if (key === 'password') passwordForm.setError("password", { type: 'server', message });
            if (key === 'password_confirmation') passwordForm.setError("password_confirmation", { type: 'server', message });
          });
        }
      } else {
        passwordForm.setError("root", { type: 'server', message: errorResponse?.data?.message || errorResponse?.error || "An unexpected error occurred." });
      }
    }
  }

  return (
    <div className="min-h-screen container mx-auto bg-[#F7FAF9] flex flex-col pt-8 sm:pt-16 items-center px-4 relative">
      <Link 
        to={ROUTES.LOGIN}
        className="absolute top-6 left-6 md:top-10 md:left-10 flex items-center text-sm font-semibold text-[#1B3C32] hover:opacity-70 transition-opacity"
      >
        <ChevronLeft className="w-4 h-4 mr-1" />
        Back to Login
      </Link>

      <AuthLogo />

      <div className="w-full max-w-[480px] bg-white rounded-[24px] shadow-sm shadow-[#1B3C32]/[0.03] p-8 md:p-10 border border-[#E5EDE9] mb-12">
        
        {step === "email" && (
          <>
            <div className="text-center mb-8">
              <h1 className="text-2xl font-bold text-[#111827] mb-2 font-['Outfit']">Reset Password</h1>
              <p className="text-[13px] text-[#6B7280]">Enter your email to receive an OTP code</p>
            </div>

            {emailForm.formState.errors.root && (
              <div className="mb-6 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600 text-center font-medium">
                {emailForm.formState.errors.root.message}
              </div>
            )}

            <form onSubmit={emailForm.handleSubmit(onEmailSubmit)} className="space-y-5">
              <div className="space-y-2 relative">
                <Label htmlFor="email" className="text-xs font-semibold text-[#374151]">Email</Label>
                <div className="relative">
                  <Mail className="absolute left-3.5 top-1/2 -translate-y-1/2 h-[18px] w-[18px] text-[#9CA3AF] pointer-events-none" />
                  <Input
                    id="email"
                    type="email"
                    placeholder="john@google.com"
                    className="pl-11 h-12 bg-[#F3F4F6] border-transparent focus-visible:ring-[#1E5F4C]/20 focus-visible:bg-white transition-colors rounded-xl text-sm"
                    {...emailForm.register("email")}
                  />
                </div>
                {emailForm.formState.errors.email && <p className="text-xs text-red-500 mt-1">{emailForm.formState.errors.email.message}</p>}
              </div>

              <Button 
                type="submit" 
                disabled={emailForm.formState.isSubmitting || isSending}
                className="w-full h-12 bg-[#1A5C4A] hover:bg-[#154637] text-white rounded-xl font-medium text-[15px] transition-colors shadow-sm group"
              >
                {emailForm.formState.isSubmitting || isSending ? "Sending..." : "Send OTP"} <ArrowRight className="w-4 h-4 ml-2 group-hover:w-5 group-hover:translate-x-0.5" />
              </Button>
            </form>
          </>
        )}

        {step === "otp" && (
          <>
            <div className="text-center mb-8">
              <h1 className="text-2xl font-bold text-[#111827] mb-2 font-['Outfit']">Enter OTP</h1>
              <p className="text-[13px] text-[#6B7280]">We've sent a 6-digit code to <br/><span className="font-semibold text-black">{email}</span></p>
            </div>

            {otpForm.formState.errors.root && (
              <div className="mb-6 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600 text-center font-medium">
                {otpForm.formState.errors.root.message}
              </div>
            )}

            <form onSubmit={otpForm.handleSubmit(onOtpSubmit)} className="space-y-5">
              <div className="space-y-2 relative">
                <Label htmlFor="otp" className="text-xs font-semibold text-[#374151]">6-Digit Code</Label>
                <div className="relative">
                  <KeyRound className="absolute left-3.5 top-1/2 -translate-y-1/2 h-[18px] w-[18px] text-[#9CA3AF] pointer-events-none" />
                  <Input
                    id="otp"
                    type="text"
                    maxLength={6}
                    placeholder="Enter 6-digit OTP"
                    className="pl-11 h-12 bg-[#F3F4F6] tracking-[0.2em] border-transparent focus-visible:ring-[#1E5F4C]/20 focus-visible:bg-white transition-colors rounded-xl text-sm font-medium"
                    {...otpForm.register("otp")}
                  />
                </div>
                {otpForm.formState.errors.otp && <p className="text-xs text-red-500 mt-1">{otpForm.formState.errors.otp.message}</p>}
              </div>

              <Button 
                type="submit" 
                disabled={otpForm.formState.isSubmitting || isVerifying}
                className="w-full h-12 bg-[#1A5C4A] hover:bg-[#154637] text-white rounded-xl font-medium text-[15px] transition-colors shadow-sm group"
              >
                {otpForm.formState.isSubmitting || isVerifying ? "Verifying..." : "Verify OTP"} <CheckCircle2 className="w-4 h-4 ml-2 group-hover:w-5 group-hover:scale-110 transition-transform" />
              </Button>

              <div className="text-center mt-4">
                <button 
                  type="button" 
                  onClick={() => setStep("email")}
                  className="text-[13px] text-gray-500 hover:text-black hover:underline"
                >
                  Change Email Address
                </button>
              </div>
            </form>
          </>
        )}

        {step === "password" && (
          <>
            <div className="text-center mb-8">
              <h1 className="text-2xl font-bold text-[#111827] mb-2 font-['Outfit']">Create New Password</h1>
              <p className="text-[13px] text-[#6B7280]">Please enter and confirm your new password.</p>
            </div>

            {passwordForm.formState.errors.root && (
              <div className="mb-6 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600 text-center font-medium">
                {passwordForm.formState.errors.root.message}
              </div>
            )}

            <form onSubmit={passwordForm.handleSubmit(onPasswordSubmit)} className="space-y-5">
              <div className="space-y-2 relative">
                <Label htmlFor="password" className="text-xs font-semibold text-[#374151]">New Password</Label>
                <div className="relative">
                  <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 h-[18px] w-[18px] text-[#9CA3AF] pointer-events-none" />
                  <Input
                    id="password"
                    type={showPassword ? "text" : "password"}
                    placeholder="••••••••"
                    className="pl-11 pr-10 h-12 bg-[#F3F4F6] border-transparent focus-visible:ring-[#1E5F4C]/20 focus-visible:bg-white transition-colors rounded-xl text-sm"
                    {...passwordForm.register("password")}
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#4B5563] transition-colors"
                  >
                    {showPassword ? <EyeOff className="h-[18px] w-[18px]" /> : <Eye className="h-[18px] w-[18px]" />}
                  </button>
                </div>
                {passwordForm.formState.errors.password && <p className="text-xs text-red-500 mt-1">{passwordForm.formState.errors.password.message}</p>}
              </div>

              <div className="space-y-2 relative">
                <Label htmlFor="password_confirmation" className="text-xs font-semibold text-[#374151]">Confirm Password</Label>
                <div className="relative">
                  <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 h-[18px] w-[18px] text-[#9CA3AF] pointer-events-none" />
                  <Input
                    id="password_confirmation"
                    type={showConfirmPassword ? "text" : "password"}
                    placeholder="••••••••"
                    className="pl-11 pr-10 h-12 bg-[#F3F4F6] border-transparent focus-visible:ring-[#1E5F4C]/20 focus-visible:bg-white transition-colors rounded-xl text-sm"
                    {...passwordForm.register("password_confirmation")}
                  />
                  <button
                    type="button"
                    onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                    className="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#4B5563] transition-colors"
                  >
                    {showConfirmPassword ? <EyeOff className="h-[18px] w-[18px]" /> : <Eye className="h-[18px] w-[18px]" />}
                  </button>
                </div>
                {passwordForm.formState.errors.password_confirmation && <p className="text-xs text-red-500 mt-1">{passwordForm.formState.errors.password_confirmation.message}</p>}
              </div>

              <Button 
                type="submit" 
                disabled={passwordForm.formState.isSubmitting || isResetting}
                className="w-full h-12 bg-[#1A5C4A] hover:bg-[#154637] text-white rounded-xl font-medium text-[15px] transition-colors shadow-sm group"
              >
                {passwordForm.formState.isSubmitting || isResetting ? "Resetting..." : "Reset Password"} <CheckCircle2 className="w-4 h-4 ml-2 group-hover:w-5 group-hover:scale-110 transition-transform" />
              </Button>
            </form>
          </>
        )}

        {step === "success" && (
          <div className="text-center py-6">
            <div className="w-16 h-16 bg-[#1A5C4A]/10 text-[#1A5C4A] rounded-full flex items-center justify-center mx-auto mb-6">
              <CheckCircle2 className="w-8 h-8" />
            </div>
            <h1 className="text-2xl font-bold text-[#111827] mb-3 font-['Outfit']">Password Changed!</h1>
            <p className="text-[14px] text-[#6B7280] mb-8">
              Your password has been reset successfully and you have been logged in. Redirecting to your dashboard...
            </p>
            <Link to={ROUTES.DASHBOARD}>
              <Button className="w-full h-12 bg-[#1A5C4A] hover:bg-[#154637] text-white rounded-xl font-medium text-[15px] transition-colors shadow-sm">
                Go to Dashboard
              </Button>
            </Link>
          </div>
        )}

      </div>
    </div>
  )
}
