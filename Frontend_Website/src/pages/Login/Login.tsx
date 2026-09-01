import { zodResolver } from "@hookform/resolvers/zod"
import { ArrowRight, ChevronLeft, Eye, EyeOff, Lock, Mail } from "lucide-react"
import { useState } from "react"
import { useForm } from "react-hook-form"
import { Link, useLocation, useNavigate } from "react-router"

import { useAppDispatch } from "@/app/hooks"
import { AuthLogo } from "@/components/shared/AuthLogo"
import { Button } from "@/components/ui/button"
import { Checkbox } from "@/components/ui/checkbox"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { useLoginMutation } from "@/features/auth/api/authApi"
import { setCredentials } from "@/features/auth/store/authSlice"
import { ROUTES } from "@/routes/routes.constants"
import { loginSchema, type LoginFormValues } from "@/types/auth.types"

type ApiValidationErrors = Record<string, string[]>
type LoginErrorResponse = {
  data?: {
    status?: boolean
    success?: boolean
    data?: ApiValidationErrors | never[]
    message?: string
  }
  error?: string
}

export default function LoginPage() {
  const [showPassword, setShowPassword] = useState(false)
  const dispatch = useAppDispatch()
  const navigate = useNavigate()
  const location = useLocation()
  const from = location.state?.from?.pathname || ROUTES.HOME
  const [loginUser, { isLoading }] = useLoginMutation()

  const {
    register,
    handleSubmit,
    setValue,
    setError,
    watch,
    formState: { errors, isSubmitting },
  } = useForm<LoginFormValues>({
    resolver: zodResolver(loginSchema),
    defaultValues: {
      rememberMe: false,
    },
  })

  // Watch for the checkbox state manually since we are using Shadcn Checkbox
  const rememberMe = watch("rememberMe")

  const onSubmit = async (data: LoginFormValues) => {
    try {
      const response = await loginUser({
        email: data.email,
        password: data.password,
      }).unwrap()

      if (response.success === false) {
        throw { data: response }
      }

      if (response.success || response.code === 200) {
        dispatch(
          setCredentials({
            token: response.data.token,
            user: {
              id: String(response.data.id),
              name:
                response.data.full_name ||
                `${response.data.first_name} ${response.data.last_name}`,
              email: response.data.email,
              avatar: response.data.avatar,
            },
            rememberMe: Boolean(data.rememberMe),
          })
        )
        navigate(from, { replace: true })
      }
    } catch (err) {
      const errorResponse = err as LoginErrorResponse

      if (
        errorResponse?.data?.status === false ||
        errorResponse?.data?.success === false
      ) {
        if (errorResponse.data.message) {
          setError("root", {
            type: "server",
            message: errorResponse.data.message,
          })
        }

        const validationErrors = errorResponse.data.data
        if (
          validationErrors &&
          typeof validationErrors === "object" &&
          !Array.isArray(validationErrors)
        ) {
          Object.keys(validationErrors).forEach((key) => {
            const message = validationErrors[key][0]

            let formKey: keyof LoginFormValues | null = null
            if (key === "email") formKey = "email"
            if (key === "password") formKey = "password"

            if (formKey) {
              setError(formKey, { type: "server", message })
            }
          })
        }
      } else {
        const genericMessage =
          errorResponse?.data?.message ||
          errorResponse?.error ||
          "An unexpected error occurred. Please try again."
        setError("root", { type: "server", message: genericMessage })
      }
    }
  }

  return (
    <div className="relative container mx-auto flex min-h-screen flex-col items-center bg-[#F7FAF9] px-4 pt-8 sm:pt-16">
      {/* Back button */}
      <Link
        to={ROUTES.HOME}
        className="absolute top-6 left-6 flex items-center text-sm font-semibold text-[#1B3C32] transition-opacity hover:opacity-70 md:top-10 md:left-10"
      >
        <ChevronLeft className="mr-1 h-4 w-4" />
        Back
      </Link>

      <AuthLogo />

      <div className="mb-12 w-full max-w-120 rounded-[24px] border border-[#E5EDE9] bg-white p-8 shadow-sm shadow-[#1B3C32]/3 md:p-10">
        <div className="mb-8 text-center">
          <h1 className="mb-2 font-['Outfit'] text-2xl font-bold text-[#111827]">
            Sign in to your account
          </h1>
          <p className="text-[13px] text-[#6B7280]">
            Access your GIHQS member dashboard
          </p>
        </div>

        {errors.root && (
          <div className="mb-6 rounded-lg border border-red-200 bg-red-50 p-3 text-center text-sm font-medium text-red-600">
            {errors.root.message}
          </div>
        )}

        <form onSubmit={handleSubmit(onSubmit)} className="space-y-5">
          <div className="relative space-y-2">
            <Label
              htmlFor="email"
              className="text-xs font-semibold text-[#374151]"
            >
              Email
            </Label>
            <div className="relative">
              <Mail className="pointer-events-none absolute top-1/2 left-3.5 h-4.5 w-4.5 -translate-y-1/2 text-[#9CA3AF]" />
              <Input
                id="email"
                type="email"
                placeholder="john@google.com"
                className="h-12 rounded-xl border-transparent bg-[#F3F4F6] pl-11 text-sm transition-colors focus-visible:bg-white focus-visible:ring-[#1E5F4C]/20"
                {...register("email")}
              />
            </div>
            {errors.email && (
              <p className="mt-1 text-xs text-red-500">
                {errors.email.message}
              </p>
            )}
          </div>

          <div className="relative space-y-2">
            <div className="flex items-center justify-between">
              <Label
                htmlFor="password"
                className="text-xs font-semibold text-[#374151]"
              >
                Password
              </Label>
              <Link
                to={ROUTES.FORGOT_PASSWORD}
                className="text-xs font-bold text-[#1E5F4C] hover:underline"
              >
                Forgot password?
              </Link>
            </div>
            <div className="relative">
              <Lock className="pointer-events-none absolute top-1/2 left-3.5 h-4.5 w-4.5 -translate-y-1/2 text-[#9CA3AF]" />
              <Input
                id="password"
                type={showPassword ? "text" : "password"}
                placeholder="Enter your password"
                className="h-12 rounded-xl border-transparent bg-[#F3F4F6] pr-11 pl-11 text-sm transition-colors focus-visible:bg-white focus-visible:ring-[#1E5F4C]/20"
                {...register("password")}
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute top-1/2 right-4 -translate-y-1/2 text-[#9CA3AF] transition-colors hover:text-[#4B5563]"
                tabIndex={-1}
              >
                {showPassword ? (
                  <EyeOff className="h-4 w-4" />
                ) : (
                  <Eye className="h-4 w-4" />
                )}
              </button>
            </div>
            {errors.password && (
              <p className="mt-1 text-xs text-red-500">
                {errors.password.message}
              </p>
            )}
          </div>

          <div className="flex items-center space-x-2 pt-1 pb-2">
            <Checkbox
              id="rememberMe"
              checked={rememberMe}
              onCheckedChange={(checked) =>
                setValue("rememberMe", checked === true)
              }
              className="rounded-lg border-[#D1D5DB] text-[#1E5F4C] focus-visible:ring-[#1E5F4C]"
            />
            <Label
              htmlFor="rememberMe"
              className="cursor-pointer text-xs leading-none font-semibold text-[#4B5563] peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
            >
              Remember me
            </Label>
          </div>

          <Button
            type="submit"
            disabled={isSubmitting || isLoading}
            className="group h-12 w-full rounded-xl bg-[#1A5C4A] text-[15px] font-medium text-white shadow-sm transition-colors hover:bg-[#154637]"
          >
            {isSubmitting || isLoading ? "Signing In..." : "Sign In"}{" "}
            <ArrowRight className="ml-2 h-4 w-4 group-hover:w-5 group-hover:translate-x-0.5" />
          </Button>
        </form>

        <div className="mt-8 text-center text-[13px] font-medium text-[#4B5563]">
          Don't have an account?{" "}
          <Link
            to={ROUTES.SIGNUP}
            className="font-bold text-primary underline underline-offset-2"
          >
            Register
          </Link>
        </div>
      </div>
    </div>
  )
}
