import { Navigate, Outlet, useLocation } from "react-router"
import { useAppSelector } from "@/app/hooks"
import { ROUTES } from "./routes.constants"

export default function PrivateRoute() {
  const token = useAppSelector((state) => state.auth.token)
  const location = useLocation()
  
  return token ? <Outlet /> : <Navigate to={ROUTES.LOGIN} state={{ from: location }} replace />
}
