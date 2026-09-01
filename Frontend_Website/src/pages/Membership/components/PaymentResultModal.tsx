import { useEffect, useState } from "react";
import { useSearchParams } from "react-router";
import {
  Dialog,
  DialogContent,
  DialogTitle,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { CheckCircle2, XCircle, ArrowRight } from "lucide-react";
import * as VisuallyHidden from "@radix-ui/react-visually-hidden";

export default function PaymentResultModal() {
  const [searchParams, setSearchParams] = useSearchParams();
  const paymentStatus = searchParams.get("payment");
  const [isOpen, setIsOpen] = useState(false);

  useEffect(() => {
    if (paymentStatus === "success" || paymentStatus === "cancel") {
      setIsOpen(true);
    }
  }, [paymentStatus]);

  const handleClose = () => {
    setIsOpen(false);
    setSearchParams((prev) => {
      prev.delete("payment");
      return prev;
    }, { replace: true, preventScrollReset: true });
  };

  const isSuccess = paymentStatus === "success";

  return (
    <Dialog open={isOpen} onOpenChange={handleClose}>
      <DialogContent className="sm:max-w-md p-0 overflow-hidden border-0 shadow-[0_20px_50px_rgba(15,47,38,0.2)] rounded-3xl" aria-describedby={undefined}>
        <VisuallyHidden.Root>
          <DialogTitle>{isSuccess ? "Payment Successful" : "Payment Cancelled"}</DialogTitle>
        </VisuallyHidden.Root>
        
        {isSuccess ? (
          <div className="relative">
            <div className="absolute inset-0 bg-[radial-gradient(106.46%_66.57%_at_67.39%_25.38%,rgba(212,170,58,0.15)_0%,rgba(12,42,31,0.11)_100%),linear-gradient(115deg,#0F2F26_0%,#133A2F_56%,#0B241D_100%)] pointer-events-none" />
            <div className="relative flex flex-col items-center px-8 py-12 text-center text-white">
              <div className="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-white/5 backdrop-blur-md border border-white/10 shadow-inner">
                <CheckCircle2 className="h-10 w-10 text-[#F4D36D]" strokeWidth={2.5} />
              </div>
              <h2 className="mb-3 text-3xl font-serif font-medium text-[#D4AA3A] tracking-wide">
                Payment Successful
              </h2>
              <p className="mb-8 text-sm text-primary/70 leading-relaxed max-w-[280px]">
                Welcome to Premium! Your payment has been processed and your account is now upgraded.
              </p>
              <Button
                onClick={handleClose}
                className="w-full h-12 rounded-full bg-[#F4D36D] text-[#0F2F26] hover:bg-[#EBCB62] font-bold text-sm transition-colors shadow-[0_10px_20px_rgba(244,211,109,0.15)] flex items-center justify-center gap-2 group"
              >
                Go to Dashboard
                <ArrowRight className="w-4 h-4 transition-transform group-hover:translate-x-1" />
              </Button>
            </div>
          </div>
        ) : (
          <div className="relative bg-white">
            <div className="flex flex-col items-center px-8 py-10 text-center">
              <div className="mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-red-50 border border-red-100">
                <XCircle className="h-8 w-8 text-red-500" strokeWidth={2.5} />
              </div>
              <h2 className="mb-3 text-2xl font-bold text-neutral-900 tracking-tight">
                Payment Cancelled
              </h2>
              <p className="mb-8 text-sm text-neutral-500 leading-relaxed max-w-[280px]">
                Your checkout process was cancelled. No charges were made to your account.
              </p>
              <Button
                onClick={handleClose}
                variant="outline"
                className="w-full h-12 rounded-full border-neutral-200 text-neutral-700 hover:bg-neutral-50 font-bold text-sm transition-colors"
              >
                Close
              </Button>
            </div>
          </div>
        )}
      </DialogContent>
    </Dialog>
  );
}
