import { CreditCard, MoreHorizontal, Plus } from "lucide-react"

const paymentMethods = [
  {
    title: "Visa •••• 4242",
    subtitle: "Expires 09/28",
    primary: true,
  },
  {
    title: "PayPal sarah.k***@email.com",
    subtitle: "Expires —",
    primary: false,
  },
]

export function SavedPaymentMethods() {
  return (
    <section className="rounded-[12px] bg-white p-7 shadow-sm">
      <div className="flex items-center justify-between gap-4">
        <h2 className="text-[18px] font-semibold text-[#14392f]">
          Saved payment methods
        </h2>
        <button className="inline-flex h-9 items-center gap-2 rounded-[7px] border border-border bg-white px-4 text-[15px] font-semibold text-[#111827] hover:bg-muted">
          <Plus className="size-4" aria-hidden="true" />
          Add method
        </button>
      </div>

      <div className="mt-8 grid gap-5 lg:grid-cols-2">
        {paymentMethods.map((method) => (
          <article
            key={method.title}
            className={`flex min-h-[84px] items-center justify-between gap-4 rounded-[12px] border bg-white px-6 ${
              method.primary ? "border-[#14392f]" : "border-border"
            }`}
          >
            <div className="flex items-center gap-4">
              <span className="flex size-11 items-center justify-center rounded-[8px] bg-[#14392f] text-white">
                <CreditCard className="size-5" aria-hidden="true" />
              </span>
              <div>
                <h3 className="text-[15px] font-semibold text-[#14392f]">
                  {method.title}
                </h3>
                <p className="mt-1 text-[14px] text-muted-foreground">
                  {method.subtitle}
                </p>
              </div>
            </div>

            <div className="flex items-center gap-4">
              {method.primary && (
                <span className="inline-flex h-6 items-center rounded-[7px] bg-[#f8efc9] px-3 text-[13px] font-medium text-[#14392f]">
                  Primary
                </span>
              )}
              <button className="text-[#111827] hover:text-[#14392f]">
                <MoreHorizontal className="size-5" aria-hidden="true" />
              </button>
            </div>
          </article>
        ))}
      </div>
    </section>
  )
}
