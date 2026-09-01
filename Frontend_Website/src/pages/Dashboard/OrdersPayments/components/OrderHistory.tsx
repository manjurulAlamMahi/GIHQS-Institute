import { useState } from "react"
import { FileText, Loader2 } from "lucide-react"
import { useGetOrderHistoryQuery, useLazyGetOrderInvoiceQuery, useRequestRefundMutation } from "@/features/profile/api/profileApi"
import { Skeleton } from "@/components/ui/skeleton"
import {
  Dialog,
  DialogContent,  
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Textarea } from "@/components/ui/textarea"
import { Button } from "@/components/ui/button"
import { toast } from "sonner"

import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"

export function OrderHistory() {
  const { data: response, isLoading } = useGetOrderHistoryQuery()
  const [getInvoice] = useLazyGetOrderInvoiceQuery()
  const [requestRefund, { isLoading: isRefunding }] = useRequestRefundMutation()
  const orders = response?.data?.order_history || []

  const [refundOrderId, setRefundOrderId] = useState<number | null>(null)
  const [refundReason, setRefundReason] = useState("")

  const [viewingId, setViewingId] = useState<number | null>(null)
  const [period, setPeriod] = useState("all-time")
  const [sort, setSort] = useState("date-desc")

  const visibleOrders = [...orders]
    .filter((order) => {
      if (period === "all-time") return true
      const date = new Date(order.date)
      const year = new Date().getFullYear()
      return period === "this-year" ? date.getFullYear() === year : date.getFullYear() === year - 1
    })
    .sort((a, b) => {
      if (sort === "date-asc") return new Date(a.date).getTime() - new Date(b.date).getTime()
      if (sort === "amount-desc") return Number(b.raw_amount) - Number(a.raw_amount)
      if (sort === "amount-asc") return Number(a.raw_amount) - Number(b.raw_amount)
      return new Date(b.date).getTime() - new Date(a.date).getTime()
    })

  const handleViewInvoice = async (id: number) => {
    // Open blank tab synchronously to prevent popup blocker
    const newWindow = window.open('', '_blank')
    if (newWindow) {
      newWindow.document.write('Loading PDF...')
    }

    try {
      setViewingId(id)
      const blob = await getInvoice(id).unwrap()
      const url = window.URL.createObjectURL(new Blob([blob], { type: 'application/pdf' }))

      if (newWindow) {
        newWindow.location.href = url
        setTimeout(() => window.URL.revokeObjectURL(url), 5000)
      } else {
        const a = document.createElement('a')
        a.href = url
        a.download = `Invoice-${id}.pdf`
        document.body.appendChild(a)
        a.click()
        a.remove()
        window.URL.revokeObjectURL(url)
      }
    } catch (error) {
      console.error("Failed to fetch invoice:", error)
      if (newWindow) {
        newWindow.document.write('Failed to load PDF.')
        setTimeout(() => newWindow.close(), 2000)
      }
    } finally {
      setViewingId(null)
    }
  }

  const handleRefundSubmit = async () => {
    if (!refundOrderId || !refundReason.trim()) {
      toast.error("Please provide a reason for refund.")
      return
    }
    
    try {
      await requestRefund({ order_id: refundOrderId, reason: refundReason }).unwrap()
      toast.success("Refund requested successfully.")
      setRefundOrderId(null)
      setRefundReason("")
    } catch (error) {
      toast.error("Failed to submit refund request.")
    }
  }

  return (
    <section className="rounded-[12px] bg-white p-7 shadow-sm">
      <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <h2 className="text-[18px] font-semibold text-[#14392f]">Order history</h2>

        <div className="flex gap-3">
          <Select value={period} onValueChange={setPeriod}>
            <SelectTrigger className="h-10! w-37.5 rounded-[8px] border-transparent bg-[#f3f4f6] text-sm font-medium shadow-none focus:ring-[#14392f]/20">
              <SelectValue placeholder="All time" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all-time">All time</SelectItem>
              <SelectItem value="this-year">This year</SelectItem>
              <SelectItem value="last-year">Last year</SelectItem>
            </SelectContent>
          </Select>
          <Select value={sort} onValueChange={setSort}>
            <SelectTrigger className="h-10! w-41.25 rounded-[8px] border-transparent bg-[#f3f4f6] text-sm font-medium shadow-none focus:ring-[#14392f]/20">
              <SelectValue placeholder="Sort by" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="date-desc">Newest first</SelectItem>
              <SelectItem value="date-asc">Oldest first</SelectItem>
              <SelectItem value="amount-desc">Amount: high to low</SelectItem>
              <SelectItem value="amount-asc">Amount: low to high</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>

      <div className="mt-7 overflow-x-auto">
        {isLoading ? (
          <div className="space-y-4 py-4">
            {[...Array(4)].map((_, i) => (
              <Skeleton key={i} className="h-12 w-full rounded-md" />
            ))}
          </div>
        ) : (
          <table className="w-full min-w-220 border-collapse text-left">
            <thead>
              <tr className="text-[14px] text-[#667085]">
                <th className="py-4 font-semibold">Order</th>
                <th className="py-4 font-semibold">Item</th>
                <th className="py-4 font-semibold">Date</th>
                <th className="py-4 font-semibold">Amount</th>
                <th className="py-4 font-semibold">Method</th>
                <th className="py-4 text-center font-semibold">Status</th>
                <th className="py-4 text-center font-semibold">Invoice</th>
                <th className="py-4 text-center font-semibold">Action</th>
              </tr>
            </thead>
            <tbody>
              {visibleOrders.length > 0 ? (
                visibleOrders.map((order) => (
                  <tr
                    key={order.id}
                    className="border-b border-border text-[16px] text-[#14392f]"
                  >
                    <td className="py-5 font-medium">{order.order_id}</td>
                    <td className="py-5">{order.item}</td>
                    <td className="py-5 text-[#667085]">{order.date}</td>
                    <td className="py-5 font-semibold">{order.amount}</td>
                    <td className="py-5 text-[#667085]">{order.method}</td>
                    <td className="py-5 text-center">
                      <span
                        className={`inline-flex h-6 min-w-27.5 items-center justify-center rounded-[7px] px-4 text-[14px] font-medium ${order.status === "Paid" || order.status === "Success"
                            ? "bg-[#d7f8e5] text-[#008a42]"
                            : "bg-[#ffdada] text-[#c62828]"
                          }`}
                      >
                        {order.status}
                      </span>
                    </td>
                    <td className="py-5 text-center">
                      <button
                        onClick={() => handleViewInvoice(order.id)}
                        disabled={viewingId === order.id}
                        className="inline-flex items-center justify-center gap-2 text-[16px] font-semibold text-[#111827] hover:text-[#14392f] disabled:opacity-50 disabled:cursor-not-allowed"
                      >
                        {viewingId === order.id ? (
                          <Loader2 className="size-4 animate-spin" aria-hidden="true" />
                        ) : (
                          <FileText className="size-4" aria-hidden="true" />
                        )}
                        PDF
                      </button>
                    </td>
                    <td className="py-5 text-center">
                      {(order.status === "Paid" || order.status === "Success") && (
                        <button
                          onClick={() => setRefundOrderId(order.id)}
                          className="text-[14px] font-semibold text-[#ff6658] hover:underline"
                        >
                          Request Refund
                        </button>
                      )}
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={7} className="py-8 text-center text-neutral-500">
                    No orders found.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        )}
      </div>

      <Dialog open={!!refundOrderId} onOpenChange={(open) => !open && setRefundOrderId(null)}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Request a Refund</DialogTitle>
            <DialogDescription>
              Please provide a reason for canceling this order and requesting a refund.
            </DialogDescription>
          </DialogHeader>
          <div className="py-4">
            <Textarea 
              placeholder="I purchased the wrong course package by mistake..."
              value={refundReason}
              onChange={(e) => setRefundReason(e.target.value)}
              className="min-h-25"
            />
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setRefundOrderId(null)}>Cancel</Button>
            <Button onClick={handleRefundSubmit} disabled={isRefunding || !refundReason.trim()} className="bg-[#ff6658] text-white hover:bg-[#e05a4e]">
              {isRefunding ? "Submitting..." : "Submit Request"}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </section>
  )
}
