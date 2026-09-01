import { store } from "@/app/store"
import { router } from "@/routes/routes"
import { Provider } from "react-redux"
import { RouterProvider } from "react-router/dom"
import { Toaster } from "sonner"
import AuthProvider from "./AuthProvider"

export default function AppProviders() {
  return (
    <Provider store={store}>
      <AuthProvider>
        <RouterProvider router={router} />
      </AuthProvider>
      <Toaster position="top-right" richColors />
    </Provider>
  )
}
