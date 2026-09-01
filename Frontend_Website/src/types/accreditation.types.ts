export interface AccreditationTag {
  id: number;
  accreditation_header_id: number;
  tagname: string;
}

export interface AccreditationKeyFact {
  id: number;
  accreditation_header_id: number;
  title: string;
  subtitle: string;
}

export interface AccreditationHeader {
  id: number;
  title1: string;
  title2: string;
  tagline: string;
  description: string;
  note: string;
  apply_btn_text: string;
  download_btn_text: string;
  download_file: string | null;
  content_file: string | null;
  injected_status: boolean;
  accreditation_tags: AccreditationTag[];
  accreditation_keyfacts: AccreditationKeyFact[];
}

export interface AccreditationHeaderResponse {
  success: boolean;
  message: string;
  data: {
    accreditation_headers: AccreditationHeader;
  };
  code: number;
}

export interface AccreditationEligibilityFeature {
  id: number
  accreditation_eligibility_id: number
  title: string
  description: string
}

export interface AccreditationEligibility {
  id: number
  title1: string
  title2: string
  description: string
  accreditation_eligibility_features: AccreditationEligibilityFeature[]
}

export interface AccreditationProcessFeature {
  id: number
  accreditation_process_id: number
  serial: string
  title: string
  subtitle: string
  description: string
}

export interface AccreditationProcess {
  id: number
  title1: string
  title2: string
  description: string
  accreditation_process_features: AccreditationProcessFeature[]
}

export interface AccreditationDomainFeature {
  id: number
  accreditation_domain_id: number
  domain_serial: string
  title: string
  description: string
}

export interface AccreditationDomain {
  id: number
  title1: string
  title2: string
  description: string
  accreditation_domain_features: AccreditationDomainFeature[]
}

export interface AccreditationInsightsFeature {
  id: number
  accreditation_insights_id: number
  title: string
  tagline: string | null
  description: string
}

export interface AccreditationInsight {
  id: number
  title1: string
  title2: string
  description: string
  accreditation_insights_features: AccreditationInsightsFeature[]
}

export interface AccreditationDetailsData {
  accreditation_eligibility: AccreditationEligibility
  accreditation_processes: AccreditationProcess
  accreditation_domains: AccreditationDomain
  accreditation_insights: AccreditationInsight
}

export interface AccreditationDetailsResponse {
  success: boolean
  message: string
  data: AccreditationDetailsData
  code: number
}

export interface AccreditationFeesPlanFeature {
  id: number
  accreditation_fees_plan_id: number
  feature: string
}

export interface AccreditationFeesPlan {
  id: number
  accreditation_fee_id: number
  title: string
  price: string
  description: string
  accreditation_fees_plan_features: AccreditationFeesPlanFeature[]
}

export interface AccreditationFees {
  id: number
  title1: string
  title2: string
  description: string
  accreditation_fees_plans: AccreditationFeesPlan[]
}

export interface AccreditationFeesResponse {
  success: boolean
  message: string
  data: {
    accreditation_fees: AccreditationFees
  }
  code: number
}

export interface AccreditationApplyHero {
  id: number
  title1: string
  title2: string
  tagline: string
  description: string
  note: string
}

export interface AccreditationEligibilitySnapshotFeature {
  id: number
  accreditation_eligibility_snapshot_id: number
  keypoints: string
  details: string
}

export interface AccreditationEligibilitySnapshot {
  id: number
  title: string
  description: string
  accreditation_eligibility_snapshot_features: AccreditationEligibilitySnapshotFeature[]
}

export interface AccreditationApplyHeroResponse {
  success: boolean
  message: string
  data: {
    accreditation_apply_hero: AccreditationApplyHero
    accreditation_eligibility_snapshot: AccreditationEligibilitySnapshot
  }
  code: number
}

export interface ApplyAccreditationResponse {
  success: boolean
  message: string
  data: {
    id: number
    applicant_category: string
    applicant_name: string
    department_division: string
    country: string
    city: string
    website_url: string
    year_established: string
    program_name: string
    program_type: string
    program_delivery_format: string
    estimated_annual_participants: string
    primary_language_of_instruction: string
    program_launch_date: string
    primary_contact_person: string
    contact_title_position: string
    email_address: string
    phone_number: string
    program_overview_doc: string | null
    governance_policy_doc: string | null
    additional_information: string
    status: string
    created_at: string
  }
  code: number
}

export interface ApplicationData {
  id: number;
  reference_number: string;
  program_name: string;
  submission_date: string;
  status: string;
  admin_notes: string | null;
  created_at: string;
}

export interface GetAccreditationApplicationsResponse {
  success: boolean;
  message: string;
  data: {
    applications: ApplicationData[];
  };
  code: number;
}

export interface ApplicationDetails {
  id: number;
  reference_number: string;
  applicant_category: string;
  applicant_name: string;
  department_division: string;
  country: string;
  city: string;
  website_url: string;
  year_established: string;
  program_name: string;
  program_type: string;
  program_delivery_format: string;
  estimated_annual_participants: string;
  primary_language_of_instruction: string;
  program_launch_date: string;
  primary_contact_person: string;
  contact_title_position: string;
  email_address: string;
  phone_number: string;
  program_overview_doc: string | null;
  governance_policy_doc: string | null;
  additional_information: string;
  status: string;
  admin_notes: string | null;
  submission_date: string;
  created_at: string;
  updated_at: string;
}

export interface GetAccreditationApplicationDetailsResponse {
  success: boolean;
  message: string;
  data: {
    application: ApplicationDetails;
  };
  code: number;
}
