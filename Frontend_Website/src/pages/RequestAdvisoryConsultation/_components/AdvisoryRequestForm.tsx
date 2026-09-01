import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import * as z from "zod";
import { useSubmitAdvisoryRequestMutation } from "@/features/advisory/api/advisoryApi";;
import { toast } from "sonner";
import { Loader2 } from "lucide-react";

import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

// Zod validation schema
const formSchema = z.object({
    organization_name: z.string().min(2, "Organization name is required"),
    full_name: z.string().min(2, "Full name is required"),
    work_email: z.string().email("Invalid email address"),
    phone_number: z.string().min(5, "Phone number is required"),
    country: z.string().min(2, "Country is required"),
    organization_type: z.string().min(2, "Required"),
    service_of_interest: z.string().min(2, "Required"),
    desired_timeline: z.string().min(2, "Required"),
    description_of_needs: z.string().min(10, "Please provide a brief description (min 10 characters)"),
});

type FormValues = z.infer<typeof formSchema>;

export default function AdvisoryRequestForm() {
    const [submitAdvisoryRequest, { isLoading }] = useSubmitAdvisoryRequestMutation();

    const {
        register,
        handleSubmit,
        setValue,
        watch,
        formState: { errors },
        reset
    } = useForm<FormValues>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            organization_name: "",
            full_name: "",
            work_email: "",
            phone_number: "",
            country: "",
            organization_type: "",
            service_of_interest: "",
            desired_timeline: "",
            description_of_needs: "",
        },
    });

    const onSubmit = async (data: FormValues) => {
        try {
            const res = await submitAdvisoryRequest(data).unwrap();
            toast.success(res.message || "Advisory request submitted successfully.");
            reset(); // Clear the form on success
        } catch (err: any) {
            toast.error(err?.data?.message || "Failed to submit advisory request.");
        }
    };

    return (
        <div className="w-full py-8 font-sans">
            <div className="bg-white border border-neutral-100 rounded-3xl p-6 md:p-10 shadow-sm space-y-8">

                <div className="space-y-2">
                    <h2 className="text-xl md:text-2xl font-bold text-neutral-900 tracking-tight">
                        Advisory Request Form
                    </h2>
                    <p className="text-xs md:text-sm text-neutral-500 leading-relaxed font-light">
                        This form is intended for organizations seeking advisory support in healthcare quality, patient safety, risk management, standards development, accreditation readiness, surveyor development, performance dashboards, and related system improvement priorities.
                    </p>
                </div>

                <form className="space-y-6" onSubmit={handleSubmit(onSubmit)}>
                    {/* Text input fields: 2-column grid */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div className="space-y-2">
                            <label className="text-xs md:text-sm font-semibold text-neutral-800">
                                Organization Name
                            </label>
                            <Input
                                type="text"
                                placeholder="Enter organization name"
                                className="h-11 rounded-xl border-neutral-200 focus-visible:ring-1 focus-visible:ring-neutral-400 focus-visible:ring-offset-0 placeholder:text-neutral-400 placeholder:text-xs"
                                {...register("organization_name")}
                            />
                            {errors.organization_name && <p className="text-xs text-red-500">{errors.organization_name.message}</p>}
                        </div>

                        {/* Field: Full Name */}
                        <div className="space-y-2">
                            <label className="text-xs md:text-sm font-semibold text-neutral-800">
                                Full Name
                            </label>
                            <Input
                                type="text"
                                placeholder="Enter your full name"
                                className="h-11 rounded-xl border-neutral-200 focus-visible:ring-1 focus-visible:ring-neutral-400 focus-visible:ring-offset-0 placeholder:text-neutral-400 placeholder:text-xs"
                                {...register("full_name")}
                            />
                            {errors.full_name && <p className="text-xs text-red-500">{errors.full_name.message}</p>}
                        </div>

                        {/* Field: Work Email */}
                        <div className="space-y-2">
                            <label className="text-xs md:text-sm font-semibold text-neutral-800">
                                Work Email
                            </label>
                            <Input
                                type="email"
                                placeholder="Enter your work email"
                                className="h-11 rounded-xl border-neutral-200 focus-visible:ring-1 focus-visible:ring-neutral-400 focus-visible:ring-offset-0 placeholder:text-neutral-400 placeholder:text-xs"
                                {...register("work_email")}
                            />
                            {errors.work_email && <p className="text-xs text-red-500">{errors.work_email.message}</p>}
                        </div>

                        {/* Field: Phone Number */}
                        <div className="space-y-2">
                            <label className="text-xs md:text-sm font-semibold text-neutral-800">
                                Phone Number
                            </label>
                            <Input
                                type="tel"
                                placeholder="Enter phone number"
                                className="h-11 rounded-xl border-neutral-200 focus-visible:ring-1 focus-visible:ring-neutral-400 focus-visible:ring-offset-0 placeholder:text-neutral-400 placeholder:text-xs"
                                {...register("phone_number")}
                            />
                            {errors.phone_number && <p className="text-xs text-red-500">{errors.phone_number.message}</p>}
                        </div>
                    </div>

                    {/* Select fields: own grid-cols-2 section, each select fills full cell width */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {/* Field: Country Input (Changed from Select to accommodate any country like Bangladesh) */}
                        <div className="space-y-2">
                            <label className="text-xs md:text-sm font-semibold text-neutral-800">
                                Country
                            </label>
                            <Input
                                type="text"
                                placeholder="Enter country"
                                className="h-11 rounded-xl border-neutral-200 focus-visible:ring-1 focus-visible:ring-neutral-400 focus-visible:ring-offset-0 placeholder:text-neutral-400 placeholder:text-xs"
                                {...register("country")}
                            />
                            {errors.country && <p className="text-xs text-red-500">{errors.country.message}</p>}
                        </div>

                        {/* Field: Type of Organization Select Menu */}
                        <div className="space-y-2 flex flex-col justify-end">
                            <label className="text-xs md:text-sm font-semibold text-neutral-800 mb-2">
                                Type of Organization
                            </label>
                            <Select onValueChange={(val) => setValue("organization_type", val)} value={watch("organization_type") || undefined}>
                                <SelectTrigger className="w-full h-11! rounded-xl border-neutral-200 focus:ring-1 focus:ring-neutral-400 focus:ring-offset-0 text-xs text-neutral-500">
                                    <SelectValue placeholder="Select organization type" />
                                </SelectTrigger>
                                <SelectContent className="rounded-xl">
                                    <SelectItem value="Hospital">Hospital / Healthcare Facility</SelectItem>
                                    <SelectItem value="Regulator">Regulatory Body</SelectItem>
                                    <SelectItem value="Accreditation Entity">Accreditation Entity</SelectItem>
                                    <SelectItem value="Education Provider">Education Provider</SelectItem>
                                    <SelectItem value="Other">Other</SelectItem>
                                </SelectContent>
                            </Select>
                            {errors.organization_type && <p className="text-xs text-red-500 mt-1">{errors.organization_type.message}</p>}
                        </div>

                        {/* Field: Service of Interest Select Menu */}
                        <div className="space-y-2 flex flex-col">
                            <label className="text-xs md:text-sm font-semibold text-neutral-800 mb-2">
                                Service of Interest
                            </label>
                            <Select onValueChange={(val) => setValue("service_of_interest", val)} value={watch("service_of_interest") || undefined}>
                                <SelectTrigger className="w-full h-11! rounded-xl border-neutral-200 focus:ring-1 focus:ring-neutral-400 focus:ring-offset-0 text-xs text-neutral-500">
                                    <SelectValue placeholder="Select service" />
                                </SelectTrigger>
                                <SelectContent className="rounded-xl">
                                    <SelectItem value="Patient Safety & Risk Management">Patient Safety & Risk Management</SelectItem>
                                    <SelectItem value="Quality Improvement Diagnostic">Quality Improvement Diagnostic</SelectItem>
                                    <SelectItem value="Dashboards & Performance Intelligence">Dashboards & Performance Intelligence</SelectItem>
                                    <SelectItem value="Standards Development & Advisory Support">Standards Development & Advisory Support</SelectItem>
                                </SelectContent>
                            </Select>
                            {errors.service_of_interest && <p className="text-xs text-red-500 mt-1">{errors.service_of_interest.message}</p>}
                        </div>

                        {/* Field: Desired Timeline Select Menu */}
                        <div className="space-y-2 flex flex-col">
                            <label className="text-xs md:text-sm font-semibold text-neutral-800 mb-2">
                                Desired Timeline
                            </label>
                            <Select onValueChange={(val) => setValue("desired_timeline", val)} value={watch("desired_timeline") || undefined}>
                                <SelectTrigger className="w-full h-11! rounded-xl border-neutral-200 focus:ring-1 focus:ring-neutral-400 focus:ring-offset-0 text-xs text-neutral-500">
                                    <SelectValue placeholder="Select timeline" />
                                </SelectTrigger>
                                <SelectContent className="rounded-xl">
                                    <SelectItem value="Immediate (Next 1-3 Months)">Immediate (Next 1-3 Months)</SelectItem>
                                    <SelectItem value="Within 3 Months">Within 3 Months</SelectItem>
                                    <SelectItem value="Mid-term (Next 3-6 Months)">Mid-term (Next 3-6 Months)</SelectItem>
                                    <SelectItem value="Future Planning">Future Planning</SelectItem>
                                </SelectContent>
                            </Select>
                            {errors.desired_timeline && <p className="text-xs text-red-500 mt-1">{errors.desired_timeline.message}</p>}
                        </div>
                    </div>

                    {/* Full Width Field: Brief Description of Your Needs */}
                    <div className="space-y-2">
                        <label className="text-xs md:text-sm font-semibold text-neutral-800">
                            Brief Description of Your Needs
                        </label>
                        <Textarea
                            rows={5}
                            placeholder="Briefly describe your advisory needs, goals, or current challenges"
                            className="rounded-xl border-neutral-200 focus-visible:ring-1 focus-visible:ring-neutral-400 focus-visible:ring-offset-0 placeholder:text-neutral-400 placeholder:text-xs resize-none"
                            {...register("description_of_needs")}
                        />
                        {errors.description_of_needs && <p className="text-xs text-red-500">{errors.description_of_needs.message}</p>}
                    </div>

                    {/* Bottom Footer Section: Disclaimer Text and Action Button */}
                    <div className="pt-4 border-t border-neutral-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <p className="text-[11px] text-neutral-400 font-light max-w-2xl leading-relaxed">
                            Thank you for your interest in GIHQS Advisory Services. Please provide concise and relevant information so we can review your request and direct it appropriately.
                        </p>
                        <div className="flex flex-col items-stretch sm:items-end gap-1 text-right shrink-0">
                            <Button
                                type="submit"
                                disabled={isLoading}
                                className="h-11 px-6 min-w-[200px] rounded-xl bg-[#113f27] hover:bg-[#0a2f1d] text-white font-semibold text-xs tracking-wide shadow-none transition-colors"
                            >
                                {isLoading ? (
                                    <><Loader2 className="mr-2 h-4 w-4 animate-spin" /> Submitting...</>
                                ) : (
                                    "Submit Advisory Request"
                                )}
                            </Button>
                            <span className="text-[10px] text-neutral-400 font-light hidden sm:block">
                                A confirmation message should appear after successful submission.
                            </span>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    );
}