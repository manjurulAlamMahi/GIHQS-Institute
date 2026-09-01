import { useGetSubscriptionQuery, useCancelSubscriptionMutation } from "@/features/profile/api/profileApi"
import { Skeleton } from "@/components/ui/skeleton"
import { Button } from "@/components/ui/button"
import { toast } from "sonner"
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog"

export default function SubscriptionPage() {
  const { data: response, isLoading, refetch } = useGetSubscriptionQuery()
  const [cancelSubscription, { isLoading: isCanceling }] = useCancelSubscriptionMutation()

  const data = response?.data
  const hasActive = data?.has_active_subscription
  const subscription = data?.subscription

  const handleCancel = async () => {
    try {
      await cancelSubscription().unwrap()
      toast.success("Subscription cancelled successfully.")
      refetch()
    } catch (error) {
      toast.error("Failed to cancel subscription.")
    }
  }

  return (
    <section className="min-h-full bg-[#f4f6f7] px-5 py-6">
      <div className="space-y-7">
        <section className="rounded-[12px] bg-white p-7 shadow-sm">
          <div className="mb-6">
            <h2 className="text-[18px] font-semibold text-[#14392f]">My Subscription</h2>
            <p className="mt-1 text-sm text-[#667085]">Manage your membership and billing details.</p>
          </div>

          {isLoading ? (
            <div className="space-y-4">
              <Skeleton className="h-32 w-full md:w-2/3 rounded-xl" />
            </div>
          ) : !hasActive || !subscription ? (
            <div className="rounded-lg border border-border bg-[#f9fafb] p-8 text-center md:w-2/3">
              <h3 className="text-lg font-medium text-[#14392f]">No Active Subscription</h3>
              <p className="mt-2 text-sm text-[#667085]">You currently do not have an active subscription plan.</p>
              <Button className="mt-6 bg-[#14392f] hover:bg-[#0f2b23] text-white">Browse Plans</Button>
            </div>
          ) : (
            <div className="rounded-xl border border-border p-6 md:w-2/3">
              <div className="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                  <div className="flex items-center gap-3">
                    <h3 className="text-xl font-bold text-[#14392f]">{subscription.package.name}</h3>
                    {subscription.status === 'active' && (
                      <span className="rounded-full bg-[#d7f8e5] px-2.5 py-0.5 text-xs font-semibold text-[#008a42]">
                        Active
                      </span>
                    )}
                  </div>
                  <p className="mt-1 font-medium text-[#667085]">{subscription.package.title}</p>
                </div>
                <div className="mt-4 text-left md:mt-0 md:text-right">
                  <p className="text-3xl font-bold text-[#14392f]">${subscription.package.price}</p>
                  <p className="text-sm text-[#667085]">per period</p>
                </div>
              </div>

              <div className="mt-8 border-t border-border pt-6">
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                  <div className="space-y-1">
                    <p className="text-sm text-[#667085]">Subscription ID: <span className="font-medium text-[#14392f]">{subscription.subscription_id}</span></p>
                    {subscription.next_renewal_date && (
                       <p className="text-sm text-[#667085]">Next renewal: <span className="font-medium text-[#14392f]">{subscription.next_renewal_date}</span></p>
                    )}
                  </div>
                  
                  <div className="mt-4 sm:mt-0">
                    <AlertDialog>
                      <AlertDialogTrigger asChild>
                        <Button variant="outline" className="text-[#ff6658] border-[#ff6658] hover:bg-[#ff6658] hover:text-white transition-colors">
                          Cancel Subscription
                        </Button>
                      </AlertDialogTrigger>
                      <AlertDialogContent>
                        <AlertDialogHeader>
                          <AlertDialogTitle>Cancel Subscription?</AlertDialogTitle>
                          <AlertDialogDescription>
                            Are you sure you want to cancel your subscription? You may lose access to premium features at the end of your current billing period.
                          </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                          <AlertDialogCancel>Keep Subscription</AlertDialogCancel>
                          <AlertDialogAction onClick={handleCancel} className="bg-[#ff6658] hover:bg-[#e05a4e] text-white" disabled={isCanceling}>
                            {isCanceling ? "Canceling..." : "Yes, Cancel"}
                          </AlertDialogAction>
                        </AlertDialogFooter>
                      </AlertDialogContent>
                    </AlertDialog>
                  </div>
                </div>
              </div>
            </div>
          )}
        </section>
      </div>
    </section>
  )
}
