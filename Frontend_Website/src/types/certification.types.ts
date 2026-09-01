export interface ApplyCertificationResponse {
  success: boolean;
  message: string;
  data: {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string;
    country: string;
    city: string;
    current_job_title: string;
    organization: string;
    linkedin_profile: string;
    years_of_experience: string;
    primary_area_of_experience: string;
    professional_role: string;
    resume_cv: string | null;
    catalogue_id: string;
    certification_title: string;
    confirm_accuracy: boolean;
    agree_policies: boolean;
    status: string;
    submitted_at: string;
  };
  code: number;
}

export interface CertificationCatalogue {
  id: number;
  title: string;
}

export interface GetCertificationCataloguesResponse {
  success: boolean;
  message: string;
  data: {
    certifications: CertificationCatalogue[];
  };
  code: number;
}
