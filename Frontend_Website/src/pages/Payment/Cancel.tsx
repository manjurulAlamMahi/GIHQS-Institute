import { XCircle, ArrowRight } from "lucide-react"
import { Link, useSearchParams } from "react-router"
import { ROUTES } from "@/routes/routes.constants"

const PaymentCancel = () => {
  const [searchParams] = useSearchParams()
  const orderId = searchParams.get("order_id")

  return (
    <main className="min-h-screen bg-primary flex items-center justify-center p-4">
      <div className="bg-white max-w-md w-full rounded-2xl shadow-sm p-8 text-center border border-neutral-100">
        <div className="mx-auto w-16 h-16 bg-[#ffdada] text-[#c62828] flex items-center justify-center rounded-full mb-6">
          <XCircle className="w-8 h-8" />
        </div>
        
        <h1 className="text-2xl font-semibold text-[#14392f] mb-2">Payment Cancelled</h1>
        <p className="text-[#667085] mb-8 text-[15px] leading-relaxed">
          Your transaction was cancelled and no charges were made. If you experienced an issue, please try again.
        </p>

        {orderId && (
          <div className="bg-[#f8faf9] rounded-xl p-4 mb-8 text-sm">
            <span className="text-[#667085] block mb-1">Order Reference ID</span>
            <span className="font-semibold text-[#14392f]">{orderId}</span>
          </div>
        )}

        <div className="flex flex-col gap-3">
          <Link
            to={ROUTES.DASHBOARD}
            className="inline-flex w-full h-12 items-center justify-center gap-2 rounded-xl border border-border bg-white px-4 font-semibold text-[#111827] hover:bg-muted transition-colors"
          >
            Return to Dashboard
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
      </div>
    </main>
  )
}

export default PaymentCancel
