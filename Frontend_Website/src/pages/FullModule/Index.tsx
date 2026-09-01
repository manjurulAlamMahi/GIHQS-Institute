import { Link, useParams } from "react-router";
import { Lock } from "lucide-react";
import { useGetCatalogueByIdQuery } from "@/features/catalogue/api/catalogueApi";
import { DocumentSkeleton } from "@/components/shared/DocumentSkeleton";
import AutoIframe from "@/components/shared/AutoIframe";
import { ROUTES } from "@/routes/routes.constants";

export default function FullModule() {
    const { id } = useParams();
    const { data: response, isLoading } = useGetCatalogueByIdQuery(id as string, { skip: !id });

    if (isLoading) {
        return (
            <main className="bg-white min-h-screen w-full">
                <DocumentSkeleton />
            </main>
        );
    }

    const catalogue = response?.data?.catalogue;

    if (!catalogue) {
        return (
            <div className="w-full container mx-auto p-12 text-center text-neutral-500">
                Module not found
            </div>
        );
    }

    // The API only returns module_file to a user who owns the catalogue, so a
    // missing file here means either "not purchased" or "not uploaded yet".
    if (!catalogue.module_file) {
        if (catalogue.has_access === false) {
            return (
                <main className="flex min-h-[60vh] w-full items-center justify-center bg-white px-6">
                    <div className="max-w-md text-center">
                        <Lock className="mx-auto mb-4 h-10 w-10 text-neutral-400" />
                        <h1 className="mb-2 text-2xl font-semibold text-[#14392f]">
                            This module is part of the course
                        </h1>
                        <p className="mb-6 text-neutral-500">
                            Enrol in {catalogue.title} to read the full module.
                        </p>
                        <Link
                            to={ROUTES.COURSE_DETAIL.replace(":id", String(catalogue.id))}
                            className="inline-flex h-11 items-center rounded-full bg-[#14392f] px-6 font-semibold text-white"
                        >
                            View course
                        </Link>
                    </div>
                </main>
            );
        }

        return (
            <div className="w-full container mx-auto p-12 text-center text-neutral-500">
                This course does not have a module file yet.
            </div>
        );
    }

    return (
        <main className="bg-white h-full w-full">
            <AutoIframe src={catalogue.module_file} fixedHeight />
        </main>
    );
}
