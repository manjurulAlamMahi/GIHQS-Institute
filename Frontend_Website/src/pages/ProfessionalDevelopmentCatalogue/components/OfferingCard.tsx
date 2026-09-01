import { Button } from "@/components/ui/button";
import { Link } from "react-router";
import { Loader2 } from "lucide-react";

export interface OfferingCardProps {
  id: string;
  type: "certification" | "course" | "webinar" | "module" | "toolkit";
  statusBadge?: "featured" | "trending" | "popular";
  title: string;
  description: string;
  features: string[];
  price: string;
  premiumPrice: string;
  actionText: "Apply" | "Enroll" | "Access" | "Enroll Now";
  actionTo?: string;
  onActionClick?: () => void;
  isActionLoading?: boolean;
  detailsTo?: string;
  colors: {
    bg: string;
    primaryButton: string;
    typeBadge: string;
  };
}

export default function OfferingCard({
  id,
  type,
  statusBadge,
  title,
  description,
  features,
  price,
  premiumPrice,
  actionText,
  actionTo,
  onActionClick,
  isActionLoading,
  detailsTo,
  colors,
}: OfferingCardProps) {
  return (
    <main className="bg-white p-5 rounded-2xl  border border-neutral-100/10 shadow-sm transition-transform duration-200 hover:-translate-y-1">
      <div
        style={{ backgroundColor: colors.bg }}
        className="rounded-2xl p-6 flex flex-col justify-between min-h-70"
      >
        {/* Top Section: Badges & Headings */}
        <div>
          <div className="flex items-center justify-between mb-5">
            {/* Main Category Badge */}
            <span
              style={{ backgroundColor: colors.typeBadge }}
              className="px-3 py-1 text-[10px] font-bold tracking-wider text-white uppercase rounded-full"
            >
              {type}
            </span>

            {/* Optional Status Badge (Featured, Trending, Popular) */}
            {statusBadge && (
              <span className="px-4 py-1 text-[10px] font-bold tracking-wider text-[#A57C1B] bg-[#F7F0DF] uppercase rounded-md">
                {statusBadge}
              </span>
            )}
          </div>

          {/* Course / Program Title */}
          <h3 className="text-xl font-serif font-semibold tracking-tight text-black mb-3 leading-snug min-h-14 line-clamp-2">
            {title}
          </h3>

          {/* Short Summary Text */}
          <p className="text-xs text-neutral-700 font-normal leading-relaxed mb-5 line-clamp-3">
            {description}
          </p>

          {/* Program Bullet Offerings */}
          <ul className="space-y-2 mb-6">
            {features.map((feature, idx) => (
              <li key={idx} className="flex items-center text-xs font-medium text-neutral-800">
                <span
                  style={{ backgroundColor: colors.primaryButton }}
                  className="w-1.5 h-1.5 rounded-full mr-2.5 shrink-0 opacity-80"
                />
                {feature}
              </li>
            ))}
          </ul>
        </div>

        
      </div>
      <div className="space-y-4 pt-4">
        <div className="text-right">
          <span className="block text-2xl font-bold text-neutral-900 leading-none">
            {price}
          </span>
          <span className="text-[10px] text-neutral-500 font-medium">
            For Premium Members: {premiumPrice}
          </span>
        </div>

        {/* Actions Button Footers */}
        <div className="grid grid-cols-2 gap-3">
          {actionTo ? (
            <Link to={actionTo}>
              <Button
                type="button"
                style={{ backgroundColor: colors.primaryButton }}
                className="w-full h-10 text-white font-semibold text-xs tracking-wide rounded-full shadow-none border-none hover:opacity-90 transition-opacity"
              >
                {actionText}
              </Button>
            </Link>
          ) : (
            <Button
              type="button"
              onClick={onActionClick}
              disabled={isActionLoading}
              style={{ backgroundColor: colors.primaryButton }}
              className="w-full h-10 text-white font-semibold text-xs tracking-wide rounded-full shadow-none border-none hover:opacity-90 transition-opacity flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed"
            >
              {isActionLoading && <Loader2 className="w-3 h-3 animate-spin" />}
              {actionText}
            </Button>
          )}

          <Link to={detailsTo ?? `/professional-development-catalogue/${id}`}>
            <Button
              type="button"
              className="w-full h-10 bg-[#E9ECEB] hover:bg-black/10 text-neutral-800 font-semibold text-xs tracking-wide rounded-full shadow-none border-none transition-colors"
            >
              View Details
            </Button>
          </Link>
        </div>
      </div>
    </main>
  );
}
