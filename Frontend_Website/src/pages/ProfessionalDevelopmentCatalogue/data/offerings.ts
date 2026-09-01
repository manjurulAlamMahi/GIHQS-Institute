import type { OfferingCardProps } from "../components/OfferingCard"
import { ROUTES } from "@/routes/routes.constants"

export interface OfferingItem extends OfferingCardProps {
  id: string
}

export const offeringsData: OfferingItem[] = [
  {
    id: "1",
    type: "certification",
    statusBadge: "featured",
    title: "AIHQSP — AI Healthcare Quality & Safety Professional",
    description: "Templates and tools that support more structured root cause analysis action planning and follow-through.",
    features: ["Downloadable resources", "Practical templates", "Implementation support"],
    price: "$45",
    premiumPrice: "$60",
    actionText: "Apply",
    actionTo: ROUTES.APPLY_CERTIFICATION,
    colors: { bg: "#EDE5D1", primaryButton: "#A57C1B", typeBadge: "#A57C1B" },
  },
  {
    id: "2",
    type: "course",
    title: "Root Cause Analysis (RCA)",
    description: "Root Cause Analysis (RCA) teaches healthcare professionals how to systematically investigate adverse...",
    features: ["Downloadable resources", "Practical templates", "Implementation support"],
    price: "$45",
    premiumPrice: "$60",
    actionText: "Enroll",
    colors: { bg: "#D2E1E0", primaryButton: "#1A5C4A", typeBadge: "#1A5C4A" },
  },
  {
    id: "3",
    type: "webinar",
    title: "Future Webinar Example",
    description: "This shows how the design can support future product types with their own visual color and badge.",
    features: ["Expandable type system", "Separate badge color", "Future-ready structure"],
    price: "$45",
    premiumPrice: "$60",
    actionText: "Enroll",
    colors: { bg: "#E8DBD3", primaryButton: "#8C4A22", typeBadge: "#8C4A22" },
  },
  {
    id: "4",
    type: "certification",
    statusBadge: "featured",
    title: "AIHQSP — AI Healthcare Quality & Safety Professional",
    description: "Templates and tools that support more structured root cause analysis action planning and follow-through.",
    features: ["Downloadable resources", "Practical templates", "Implementation support"],
    price: "$45",
    premiumPrice: "$60",
    actionText: "Apply",
    actionTo: ROUTES.APPLY_CERTIFICATION,
    colors: { bg: "#EDE5D1", primaryButton: "#A57C1B", typeBadge: "#A57C1B" },
  },
  {
    id: "5",
    type: "course",
    title: "Root Cause Analysis (RCA)",
    description: "Root Cause Analysis (RCA) teaches healthcare professionals how to systematically investigate adverse...",
    features: ["Downloadable resources", "Practical templates", "Implementation support"],
    price: "$45",
    premiumPrice: "$60",
    actionText: "Enroll",
    colors: { bg: "#D2E1E0", primaryButton: "#1A5C4A", typeBadge: "#1A5C4A" },
  },
  {
    id: "6",
    type: "webinar",
    title: "Future Webinar Example",
    description: "This shows how the design can support future product types with their own visual color and badge.",
    features: ["Expandable type system", "Separate badge color", "Future-ready structure"],
    price: "$45",
    premiumPrice: "$60",
    actionText: "Enroll",
    colors: { bg: "#E8DBD3", primaryButton: "#8C4A22", typeBadge: "#8C4A22" },
  },
]
