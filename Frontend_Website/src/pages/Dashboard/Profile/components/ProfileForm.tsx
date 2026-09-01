import { useEffect, useRef, useState } from "react"
import { useForm } from "react-hook-form"
import { zodResolver } from "@hookform/resolvers/zod"
import * as z from "zod"
import { Mail, MapPin, Phone, User } from "lucide-react"
import { toast } from "sonner"
import { useDispatch, useSelector } from "react-redux"

import { Avatar, AvatarFallback } from "@/components/ui/avatar"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { useQueryModal } from "@/hooks/useQueryModal"
import { useGetProfileInfoQuery, useUpdateProfileMutation, useChangePasswordMutation } from "@/features/auth/api/authApi"
import { updateUser } from "@/features/auth/store/authSlice"
import { Skeleton } from "@/components/ui/skeleton"
import type { RootState } from "@/app/rootReducer"

const profileSchema = z.object({
  first_name: z.string().min(1, "First name is required"),
  last_name: z.string().min(1, "Last name is required"),
  email: z.string().email("Invalid email").optional().nullable(),
  phone: z.string().optional().nullable(),
  address: z.string().optional().nullable(),
  bio: z.string().optional().nullable(),
})

const passwordSchema = z.object({
  old_password: z.string().min(1, "Current password is required"),
  new_password: z.string().min(8, "Password must be at least 8 characters"),
  new_password_confirmation: z.string().min(8, "Password must be at least 8 characters"),
}).refine((data) => data.new_password === data.new_password_confirmation, {
  message: "Passwords don't match",
  path: ["new_password_confirmation"],
})

type ProfileFormValues = z.infer<typeof profileSchema>
type PasswordFormValues = z.infer<typeof passwordSchema>

export function ProfileForm() {
  const dispatch = useDispatch()
  const user = useSelector((state: RootState) => state.auth.user)
  
  const personalTab = useQueryModal("tab", "personal")
  const securityTab = useQueryModal("tab", "security")
  const isSecurity = securityTab.isOpen
  const isPersonal = personalTab.isOpen || !isSecurity

  const fileInputRef = useRef<HTMLInputElement>(null)
  const [selectedFile, setSelectedFile] = useState<File | null>(null)
  const [previewUrl, setPreviewUrl] = useState<string | null>(null)

  const { data: profileResponse, isLoading, refetch, error } = useGetProfileInfoQuery()
  const [updateProfileApi, { isLoading: isUpdating }] = useUpdateProfileMutation()
  const [changePasswordApi, { isLoading: isChangingPassword }] = useChangePasswordMutation()

  const profileData = profileResponse?.data

  useEffect(() => {
    if (error) {
      console.error("Profile GET Error:", error)
    }
    if (profileResponse?.data) {
      // Sync global user state so Navbar and other components get the latest Avatar & Name immediately
      dispatch(updateUser({
        ...user,
        ...profileResponse.data,
      } as any))
    }
  }, [error, profileResponse, dispatch])

  const { register, handleSubmit, reset, formState: { errors } } = useForm<ProfileFormValues>({
    resolver: zodResolver(profileSchema),
    defaultValues: {
      first_name: "",
      last_name: "",
      email: "",
      phone: "",
      address: "",
      bio: "",
    }
  })

  const { 
    register: registerPassword, 
    handleSubmit: handlePasswordSubmit, 
    reset: resetPassword, 
    formState: { errors: passwordErrors } 
  } = useForm<PasswordFormValues>({
    resolver: zodResolver(passwordSchema),
    defaultValues: {
      old_password: "",
      new_password: "",
      new_password_confirmation: "",
    }
  })

  useEffect(() => {
    if (profileData) {
      reset({
        first_name: profileData.first_name || "",
        last_name: profileData.last_name || "",
        email: profileData.email || "",
        phone: profileData.phone || "",
        address: profileData.address || "",
        bio: (profileData as any).bio || "",
      })
    }
  }, [profileData, reset])

  useEffect(() => {
    if (profileData?.avatar && !selectedFile) {
      setPreviewUrl(profileData.avatar)
    }
  }, [profileData?.avatar, selectedFile])

  const handleFileSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files[0]) {
      const file = e.target.files[0]
      setSelectedFile(file)
      setPreviewUrl(URL.createObjectURL(file))
    }
  }

  const onSubmit = async (values: ProfileFormValues) => {
    const formData = new FormData()
    
    formData.append("first_name", values.first_name || "")
    formData.append("last_name", values.last_name || "")
    formData.append("phone", values.phone || "")
    formData.append("address", values.address || "")
    formData.append("bio", values.bio || "")

    // Preserve fields that are hidden from the UI but may be required by the backend
    if (profileData?.username) formData.append("username", profileData.username)
    if (profileData?.city) formData.append("city", profileData.city)
    if (profileData?.zip) formData.append("zip", profileData.zip)
    if (profileData?.country) formData.append("country", profileData.country)
    
    if (selectedFile) {
      formData.append("avatar", selectedFile)
    }

    try {
      const response = await updateProfileApi(formData).unwrap()
      toast.success(response.message || "Profile updated successfully")
      refetch()
      
      if (user && response.data) {
        // Sync Redux state so the header icon updates immediately
        dispatch(
          updateUser({
            ...user,
            name: response.data.full_name || `${response.data.first_name} ${response.data.last_name}`,
            email: response.data.email,
            avatar: response.data.avatar,
          })
        )
      }
    } catch (err) {
      const error = err as { data?: { message?: string } }
      toast.error(error.data?.message || "Failed to update profile")
    }
  }

  const onPasswordSubmit = async (values: PasswordFormValues) => {
    try {
      const response = await changePasswordApi(values).unwrap()
      toast.success(response.message || "Password updated successfully")
      resetPassword()
    } catch (err) {
      const error = err as { data?: { message?: string } }
      toast.error(error.data?.message || "Failed to change password")
    }
  }

  if (isLoading) {
    return (
      <section className="rounded-[12px] border border-border bg-white p-8 shadow-sm">
        {/* Header Section */}
        <div className="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
          <div className="flex items-center gap-5">
            <Skeleton className="size-20 shrink-0 rounded-full bg-gray-200" />
            <div>
              <Skeleton className="h-7 w-48 bg-gray-200" />
              <Skeleton className="mt-2 h-4 w-24 bg-gray-200" />
            </div>
          </div>
          <Skeleton className="h-10 w-[124px] rounded-[8px] bg-gray-200" />
        </div>

        {/* Tabs */}
        <div className="mt-16 inline-grid w-full max-w-[340px] grid-cols-2 rounded-[12px] bg-[#e8e8ec] p-1 gap-1">
          <Skeleton className="h-8 rounded-[10px] bg-white shadow-sm" />
          <Skeleton className="h-8 rounded-[10px] bg-transparent" />
        </div>

        {/* Form Fields */}
        <div className="mt-6 space-y-5">
          <div className="grid gap-5 md:grid-cols-2">
            {Array.from({ length: 8 }).map((_, i) => (
              <div key={i} className="space-y-2">
                <Skeleton className="h-4 w-24 bg-gray-200" />
                <Skeleton className="h-10 w-full rounded-[8px] bg-[#f0f0f2]" />
              </div>
            ))}
          </div>

          <div className="flex justify-end gap-3 pt-8">
            <Skeleton className="h-10 w-24 rounded-[8px] bg-gray-200" />
            <Skeleton className="h-10 w-32 rounded-[8px] bg-[#14392f]/20" />
          </div>
        </div>
      </section>
    )
  }

  const displayAvatar = previewUrl || (profileData?.avatar) || ""
  const displayInitials = profileData ? (profileData.first_name?.[0] || profileData.full_name?.[0] || "U").toUpperCase() : "U"
  const displayName = profileData?.full_name || `${profileData?.first_name || ""} ${profileData?.last_name || ""}`.trim() || "User"

  return (
    <section className="rounded-[12px] border border-border bg-white p-8 shadow-sm">
      <div className="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
        <div className="flex items-center gap-5">
          <Avatar className="size-20 ring-2 ring-primary/10">
            {displayAvatar ? (
              <img src={displayAvatar} alt="Profile" className="aspect-square size-full rounded-full object-cover" />
            ) : (
              <AvatarFallback className="bg-[#ddb737] text-2xl font-semibold text-[#14392f] uppercase">
                {displayInitials}
              </AvatarFallback>
            )}
          </Avatar>
          <div>
            <h1 className="text-[22px] font-semibold text-[#14392f]">
              {displayName}
            </h1>
            <p className="mt-1 text-[15px] text-muted-foreground">
              Member since March 2026
            </p>
          </div>
        </div>

        <div className="flex flex-col gap-2">
          <input 
            type="file" 
            ref={fileInputRef} 
            onChange={handleFileSelect} 
            accept="image/*" 
            className="hidden" 
          />
          <Button
            type="button"
            variant="outline"
            onClick={() => fileInputRef.current?.click()}
            className="h-10 rounded-[8px] bg-white px-5 text-[15px] font-medium"
          >
            Change photo
          </Button>
          {selectedFile && (
             <span className="text-xs text-muted-foreground text-center truncate max-w-[120px]">
               {selectedFile.name}
             </span>
          )}
        </div>
      </div>

      <div className="mt-16 inline-grid w-full max-w-[340px] grid-cols-2 rounded-[12px] bg-[#e8e8ec] p-1">
        <button
          type="button"
          onClick={() => personalTab.open()}
          className={`h-8 rounded-[10px] text-[15px] font-medium text-[#111827] ${
            isPersonal ? "bg-white shadow-sm" : ""
          }`}
        >
          Personal
        </button>
        <button
          type="button"
          onClick={() => securityTab.open()}
          className={`h-8 rounded-[10px] text-[15px] font-medium text-[#111827] ${
            isSecurity ? "bg-white shadow-sm" : ""
          }`}
        >
          Security
        </button>
      </div>

      {isPersonal ? (
        <form onSubmit={handleSubmit(onSubmit)} className="mt-6 space-y-5">
          <div className="grid gap-5 md:grid-cols-2">
            <label className="space-y-2">
              <span className="flex items-center gap-2 text-[15px] font-medium text-[#111827]">
                <User className="size-3.5" aria-hidden="true" />
                First name
              </span>
              <Input
                {...register("first_name")}
                className="h-10 rounded-[8px] border-transparent bg-[#f0f0f2] text-[15px] shadow-none focus-visible:ring-[#14392f]/20"
              />
              {errors.first_name && <p className="text-xs text-red-500">{errors.first_name.message}</p>}
            </label>

            <label className="space-y-2">
              <span className="flex items-center gap-2 text-[15px] font-medium text-[#111827]">
                <User className="size-3.5" aria-hidden="true" />
                Last name
              </span>
              <Input
                {...register("last_name")}
                className="h-10 rounded-[8px] border-transparent bg-[#f0f0f2] text-[15px] shadow-none focus-visible:ring-[#14392f]/20"
              />
              {errors.last_name && <p className="text-xs text-red-500">{errors.last_name.message}</p>}
            </label>

            <label className="space-y-2">
              <span className="flex items-center gap-2 text-[15px] font-medium text-[#111827]">
                <Mail className="size-3.5" aria-hidden="true" />
                Email
              </span>
              <Input
                {...register("email")}
                disabled
                className="h-10 rounded-[8px] border-transparent bg-[#f0f0f2] opacity-70 text-[15px] shadow-none focus-visible:ring-[#14392f]/20"
              />
            </label>

            <label className="space-y-2">
              <span className="flex items-center gap-2 text-[15px] font-medium text-[#111827]">
                <Phone className="size-3.5" aria-hidden="true" />
                Phone
              </span>
              <Input
                {...register("phone")}
                className="h-10 rounded-[8px] border-transparent bg-[#f0f0f2] text-[15px] shadow-none focus-visible:ring-[#14392f]/20"
              />
            </label>
            
            <label className="space-y-2 md:col-span-2">
              <span className="flex items-center gap-2 text-[15px] font-medium text-[#111827]">
                <MapPin className="size-3.5" aria-hidden="true" />
                Address
              </span>
              <Input
                {...register("address")}
                className="h-10 rounded-[8px] border-transparent bg-[#f0f0f2] text-[15px] shadow-none focus-visible:ring-[#14392f]/20"
              />
            </label>

            <label className="space-y-2 md:col-span-2">
              <span className="flex items-center gap-2 text-[15px] font-medium text-[#111827]">
                Bio
              </span>
              <textarea
                {...register("bio")}
                className="h-24 w-full resize-none rounded-[8px] border-transparent bg-[#f0f0f2] p-3 text-[15px] shadow-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#14392f]/20"
              />
            </label>
          </div>

          <div className="flex justify-end gap-3 pt-8">
            <Button
              type="button"
              variant="outline"
              onClick={() => {
                reset()
                setSelectedFile(null)
                if (profileData?.avatar) setPreviewUrl(profileData.avatar)
                else setPreviewUrl(null)
              }}
              disabled={isUpdating}
              className="h-10 rounded-[8px] bg-white px-5 text-[15px] font-medium"
            >
              Cancel
            </Button>
            <Button
              type="submit"
              disabled={isUpdating}
              className="h-10 rounded-[8px] bg-[#14392f] px-5 text-[15px] font-medium text-white hover:bg-[#0f2f26]"
            >
              {isUpdating ? "Saving..." : "Save changes"}
            </Button>
          </div>
        </form>
      ) : (
        <form onSubmit={handlePasswordSubmit(onPasswordSubmit)} className="mt-6 space-y-5">
          <label className="block space-y-2">
            <span className="text-[15px] font-medium text-[#111827]">
              Current password
            </span>
            <Input
              type="password"
              {...registerPassword("old_password")}
              className="h-10 rounded-[8px] border-transparent bg-[#f0f0f2] text-[15px] shadow-none focus-visible:ring-[#14392f]/20"
            />
            {passwordErrors.old_password && <p className="text-xs text-red-500">{passwordErrors.old_password.message}</p>}
          </label>

          <div className="grid gap-5 md:grid-cols-2">
            <label className="space-y-2">
              <span className="text-[15px] font-medium text-[#111827]">
                New password
              </span>
              <Input
                type="password"
                {...registerPassword("new_password")}
                className="h-10 rounded-[8px] border-transparent bg-[#f0f0f2] text-[15px] shadow-none focus-visible:ring-[#14392f]/20"
              />
              {passwordErrors.new_password && <p className="text-xs text-red-500">{passwordErrors.new_password.message}</p>}
            </label>
            <label className="space-y-2">
              <span className="text-[15px] font-medium text-[#111827]">
                Confirm
              </span>
              <Input
                type="password"
                {...registerPassword("new_password_confirmation")}
                className="h-10 rounded-[8px] border-transparent bg-[#f0f0f2] text-[15px] shadow-none focus-visible:ring-[#14392f]/20"
              />
              {passwordErrors.new_password_confirmation && <p className="text-xs text-red-500">{passwordErrors.new_password_confirmation.message}</p>}
            </label>
          </div>

          <div className="flex justify-end gap-3 pt-8">
            <Button
              type="button"
              variant="outline"
              onClick={() => resetPassword()}
              className="h-10 rounded-[8px] bg-white px-5 text-[15px] font-medium"
            >
              Cancel
            </Button>
            <Button
              type="submit"
              disabled={isChangingPassword}
              className="h-10 rounded-[8px] bg-[#14392f] px-5 text-[15px] font-medium text-white hover:bg-[#0f2f26]"
            >
              {isChangingPassword ? "Updating..." : "Update Password"}
            </Button>
          </div>
        </form>
      )}
    </section>
  )
}
