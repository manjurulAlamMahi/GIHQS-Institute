import  { useState } from "react"
import { useForm } from "react-hook-form"
import { zodResolver } from "@hookform/resolvers/zod"
import { Link, useNavigate, useLocation } from "react-router"
import { Eye, EyeOff, Mail, ChevronLeft, ChevronDown, ArrowRight } from "lucide-react"
import { toast } from "sonner"

import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Checkbox } from "@/components/ui/checkbox"
import { Button } from "@/components/ui/button"
import { AuthLogo } from "@/components/shared/AuthLogo"
import { ROUTES } from "@/routes/routes.constants"
import { useRegisterMutation } from "@/features/auth/api/authApi"
import { signupSchema, type SignupFormValues } from "@/types/auth.types"

type ApiValidationErrors = Record<string, string[]>
type SignupErrorResponse = {
  data?: {
    status?: boolean
    success?: boolean
    data?: ApiValidationErrors | never[]
    message?: string
  }
  error?: string
}

export default function SignupPage() {
  const [showPassword, setShowPassword] = useState(false)
  const navigate = useNavigate()
  const location = useLocation()
  const from = location.state?.from?.pathname || ROUTES.HOME
  const [registerUser, { isLoading }] = useRegisterMutation()

  const {
    register,
    handleSubmit,
    setValue,
    setError,
    watch,
    formState: { errors, isSubmitting },
  } = useForm<SignupFormValues>({
    resolver: zodResolver(signupSchema),
    defaultValues: {
      country: "",
      rememberMe: false,
    },
  })

  const rememberMe = watch("rememberMe")

  const onSubmit = async (data: SignupFormValues) => {
    try {
      const response = await registerUser({
        first_name: data.firstName,
        last_name: data.lastName,
        country: data.country || "",
        email: data.email,
        password: data.password,
      }).unwrap();
      
      // If the API returns HTTP 200 but it's actually an error response
      if (response.success === false) {
        throw { data: response };
      }
      
      if (response.success || response.code === 200 || response.code === 201) {
        toast.success(response.message || "Registration successful. OTP sent to your email.")
        navigate(ROUTES.VERIFY_EMAIL, {
          replace: true,
          state: {
            email: response.data?.email || data.email,
            from,
            rememberMe: Boolean(data.rememberMe),
          },
        })
      }
    } catch (err) {
      const errorResponse = err as SignupErrorResponse;
      
      if (errorResponse?.data?.status === false || errorResponse?.data?.success === false) {
        if (errorResponse.data.message) {
          setError("root", { type: 'server', message: errorResponse.data.message });
        }

        const validationErrors = errorResponse.data.data;
        if (validationErrors && typeof validationErrors === 'object' && !Array.isArray(validationErrors)) {
          Object.keys(validationErrors).forEach((key) => {
            const message = validationErrors[key][0];
            
            let formKey: keyof SignupFormValues | null = null;
            if (key === 'email') formKey = 'email';
            if (key === 'first_name') formKey = 'firstName';
            if (key === 'last_name') formKey = 'lastName';
            if (key === 'country') formKey = 'country';
            if (key === 'password') formKey = 'password';

            if (formKey) {
              setError(formKey, { type: 'server', message });
            }
          });
        }
      } else {
        // Fallback for network errors, 500s, 404s, or unhandled validation errors
        const genericMessage = errorResponse?.data?.message || errorResponse?.error || "An unexpected error occurred. Please try again.";
        setError("root", { type: 'server', message: genericMessage });
      }
    }
  }

  return (
    <div className="min-h-screen container mx-auto bg-[#F8FAFA] flex flex-col pt-8 sm:pt-16 items-center px-4 relative pb-10">
      {/* Back button */}
      <Link 
        to={ROUTES.HOME}
        className="absolute top-6 left-6 md:top-10 md:left-10 flex items-center text-sm font-semibold text-[#1B3C32] hover:opacity-70 transition-opacity"
      >
        <ChevronLeft className="w-4 h-4 mr-1" />
        Back
      </Link>

      <AuthLogo />

      <div className="w-full max-w-125 bg-white rounded-[24px] shadow-sm shadow-[#1B3C32]/3 p-8 md:p-10 border border-[#E5EDE9]">
        <div className="text-center mb-8">
          <h1 className="text-2xl font-bold text-[#111827] mb-2 font-['Outfit']">Create your Free Account</h1>
          <p className="text-[13px] text-[#6B7280] max-w-sm mx-auto leading-relaxed">
            Welcome! Registering gives you access to world-class resources, free templates, free e-learning and much more.
          </p>
        </div>

        {errors.root && (
          <div className="mb-6 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600 text-center font-medium">
            {errors.root.message}
          </div>
        )}

        <form onSubmit={handleSubmit(onSubmit)} className="space-y-5">
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <Label htmlFor="firstName" className="text-xs font-semibold text-[#374151]">First Name</Label>
              <Input
                id="firstName"
                placeholder="Enter your first name"
                className="h-12 bg-[#F3F4F6] border-transparent transition-colors rounded-xl text-sm placeholder:text-[#9CA3AF]"
                {...register("firstName")}
              />
              {errors.firstName && <p className="text-xs text-red-500">{errors.firstName.message}</p>}
            </div>
            <div className="space-y-2">
              <Label htmlFor="lastName" className="text-xs font-semibold text-[#374151]">Last Name</Label>
              <Input
                id="lastName"
                placeholder="Enter your last name"
                className="h-12 bg-[#F3F4F6] border-transparent focus-visible:ring-[#1E5F4C]/20 focus-visible:bg-white transition-colors rounded-xl text-sm placeholder:text-[#9CA3AF]"
                {...register("lastName")}
              />
              {errors.lastName && <p className="text-xs text-red-500">{errors.lastName.message}</p>}
            </div>
          </div>

          <div className="space-y-2 relative">
            <Label htmlFor="country" className="text-xs font-semibold text-[#374151]">Select Country</Label>
            <div className="relative">
              {/* For simplicity we'll use a native select styled properly or an input masquerading as select */}
              <select
                id="country"
                className="w-full h-12 bg-[#F3F4F6] border-transparent focus-visible:ring-[#1E5F4C]/20 focus-visible:bg-white transition-colors rounded-xl text-sm px-3.5 appearance-none text-[#9CA3AF] cursor-pointer outline-none"
                {...register("country")}
              >
                <option value="" disabled hidden>Select Country</option>
                <option value="US" className="text-black">United States</option>
                <option value="UK" className="text-black">United Kingdom</option>
                <option value="AE" className="text-black">United Arab Emirates</option>
                <option value="SA" className="text-black">Saudi Arabia</option>
              </select>
              <ChevronDown className="absolute right-4 top-1/2 -translate-y-1/2 h-4 w-4 text-[#9CA3AF] pointer-events-none" />
            </div>
            {errors.country && <p className="text-xs text-red-500">{errors.country.message}</p>}
          </div>

          <div className="space-y-2 relative">
            <Label htmlFor="email" className="text-xs font-semibold text-[#374151]">Email</Label>
            <div className="relative">
              <Mail className="absolute right-4 top-1/2 -translate-y-1/2 h-4.5 w-4.5 text-[#9CA3AF] pointer-events-none" />
              <Input
                id="email"
                type="email"
                placeholder="Enter your mail"
                className="pr-11 pl-3.5 h-12 bg-[#F3F4F6] border-transparent focus-visible:ring-[#1E5F4C]/20 focus-visible:bg-white transition-colors rounded-xl text-sm placeholder:text-[#9CA3AF]"
                {...register("email")}
              />
            </div>
            {errors.email && <p className="text-xs text-red-500 mt-1">{errors.email.message}</p>}
          </div>

          <div className="space-y-2 relative">
            <Label htmlFor="password" className="text-xs font-semibold text-[#374151]">Password</Label>
            <div className="relative">
              <Input
                id="password"
                type={showPassword ? "text" : "password"}
                placeholder="Enter your password"
                className="pr-11 pl-3.5 h-12 bg-[#F3F4F6] border-transparent focus-visible:ring-[#1E5F4C]/20 focus-visible:bg-white transition-colors rounded-xl text-sm placeholder:text-[#9CA3AF]"
                {...register("password")}
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute right-4 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#4B5563] transition-colors"
                tabIndex={-1}
              >
                {showPassword ? <EyeOff className="h-4.5 w-4.5" /> : <Eye className="h-4.5 w-4.5" />}
              </button>
            </div>
            {errors.password && <p className="text-xs text-red-500 mt-1">{errors.password.message}</p>}
          </div>

          <div className="flex items-center space-x-2 pt-1 pb-2">
            <Checkbox 
              id="rememberMeSignup" 
              checked={rememberMe}
              onCheckedChange={(checked) => setValue("rememberMe", checked === true)}
              className="rounded-[4px] border-[#D1D5DB] text-[#1E5F4C] focus-visible:ring-[#1E5F4C]"
            />
            <Label
              htmlFor="rememberMeSignup"
              className="text-xs font-semibold text-[#4B5563] leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer"
            >
              Remember me
            </Label>
          </div>

          <Button 
            type="submit" 
            disabled={isSubmitting || isLoading}
            className="w-full h-12 bg-[#1A5C4A] hover:bg-[#154637] text-white rounded-xl font-medium text-[15px] transition-colors shadow-sm group"
          >
            {isSubmitting || isLoading ? "Creating..." : "Create Account "} <ArrowRight className="w-4 h-4 ml-2 group-hover:w-5 group-hover:translate-x-0.5" />
          </Button>
        </form>

        <div className="mt-8 text-center text-[13px] text-[#4B5563] font-medium">
          Already registered?{" "}
          <Link to={ROUTES.LOGIN} className="text-primary font-bold underline underline-offset-2">
            Sign In Now
          </Link>
        </div>
      </div>
    </div>
  )
}
