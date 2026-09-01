import { Skeleton } from "@/components/ui/skeleton";

export function DocumentSkeleton() {
  return (
    <div className="mx-auto max-w-380 px-4 md:px-8 py-10 md:py-16 w-full bg-white min-h-screen">
      <Skeleton className="h-10 md:h-12 w-[80%] md:w-1/2 mb-8 md:mb-10 rounded-xl bg-neutral-100" />
      
      <div className="space-y-4 mb-14">
        <Skeleton className="h-4 w-full bg-neutral-100" />
        <Skeleton className="h-4 w-[92%] bg-neutral-100" />
        <Skeleton className="h-4 w-[96%] bg-neutral-100" />
        <Skeleton className="h-4 w-[85%] bg-neutral-100" />
      </div>
      
      <Skeleton className="h-8 w-1/3 mb-8 rounded-lg bg-neutral-100" />
      
      <div className="space-y-4 mb-14">
        <Skeleton className="h-4 w-[95%] bg-neutral-100" />
        <Skeleton className="h-4 w-[88%] bg-neutral-100" />
        <Skeleton className="h-4 w-full bg-neutral-100" />
        <Skeleton className="h-4 w-[75%] bg-neutral-100" />
      </div>

      <Skeleton className="h-62.5 md:h-100 w-full rounded-2xl bg-neutral-100 mb-10 md:mb-14" />
      
      <div className="space-y-4">
        <Skeleton className="h-4 w-full bg-neutral-100" />
        <Skeleton className="h-4 w-[90%] bg-neutral-100" />
      </div>
    </div>
  );
}
