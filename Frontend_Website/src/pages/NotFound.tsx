import { Link } from "react-router"

export default function NotFoundPage() {
  return (
    <div className="container mx-auto p-4 text-center mt-20">
      <h1 className="text-5xl font-bold mb-4 text-destructive">404</h1>
      <p className="text-xl mb-8">Page Not Found</p>
      <Link to="/" className="text-primary hover:underline">
        Go back home
      </Link>
    </div>
  )
}
