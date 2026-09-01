import React, { useEffect } from "react"
import { useAppDispatch, useAppSelector } from "@/app/hooks"
import { useGetProfileInfoQuery } from "@/features/auth/api/authApi"
import { updateUser, logout } from "@/features/auth/store/authSlice"

export default function AuthProvider({
  children,
}: {
  children: React.ReactNode
}) {
  const dispatch = useAppDispatch()
  const token = useAppSelector((state) => state.auth.token)

  const { data: profileData, isError } = useGetProfileInfoQuery(undefined, {
    skip: !token,
  })

  useEffect(() => {
    if (profileData?.data) {
      dispatch(
        updateUser({
          id: String(profileData.data.id),
          name:
            profileData.data.full_name ||
            `${profileData.data.first_name} ${profileData.data.last_name}`,
          email: profileData.data.email,
          avatar: profileData.data.avatar,
        })
      )
    }
  }, [profileData, dispatch])

  useEffect(() => {
    if (isError) {
      // If token is invalid or expired, log the user out
      dispatch(logout())
    }
  }, [isError, dispatch])

  return <>{children}</>
}
