import { OrderHistory } from "./components/OrderHistory"

const OrdersPaymentsPage = () => {
  return (
    <section className="min-h-full bg-[#f4f6f7] px-5 py-6">
      <div className="space-y-7">
        <OrderHistory />
      </div>
    </section>
  )
}

export default OrdersPaymentsPage
