export interface CatalogueFeature {
  id: number
  catalogue_id: number
  description: string
}

export interface CatalogueResource {
  id: number
  catalogue_id: number
  resource_title: string
  resource_file: string
  is_premium: boolean
}

export interface CatalogueExam {
  id: number
  catalogue_id: number
  exam_title: string
  exam_link: string | null
  is_premium: boolean
}

export interface Catalogue {
  id: number
  title: string
  short_title: string
  short_description: string
  price_regular: number
  price_member: number
  service_type: string
  details_file: string | null
  /** Paid material - null unless the API confirms the viewer owns the catalogue. */
  story_guide_file: string | null
  module_file: string | null
  overview_video?: string | null
  /** True when the viewer has bought this catalogue (or it is covered by their membership). */
  has_access?: boolean
  is_feature: boolean
  is_trending: boolean
  is_popular: boolean
  healthcare_quality_improvement: boolean
  patient_safety_risk_management: boolean
  status: number
  credit_earn?: number
  ce_credit_total_required?: number
  certification_approved?: boolean
  features: CatalogueFeature[]
  resources: CatalogueResource[]
  exams: CatalogueExam[]
}

export interface CataloguesResponse {
  success: boolean
  message: string
  data: {
    catalogues: Catalogue[]
  }
  code: number
}

export interface SingleCatalogueResponse {
  success: boolean
  message: string
  data: {
    catalogue: Catalogue
  }
  code: number
}

export interface CheckoutRequest {
  catalogue_id: number
}

export interface CheckoutResponse {
  success: boolean
  message: string
  data: {
    redirect_url: string
    session_id: string
    purchase_id: number
    order_id: string
    catalogue: {
      id: number
      title: string
      price: number
      price_type: string
    }
  }
  code: number
}

export interface CertificationCatalogue {
  id: number
  title: string
}

export interface CertificationCataloguesResponse {
  success: boolean
  message: string
  data: {
    certifications: CertificationCatalogue[]
  }
  code: number
}

export interface MenuItem {
  id: number
  name: string
  title: string
  short_title: string
  details_file: string | null
  story_guide_file: string | null
}

export interface MenuCataloguesResponse {
  success: boolean
  message: string
  data: {
    modules: {
      healthcare_quality_improvement: MenuItem[]
      patient_safety_risk_management: MenuItem[]
      others: MenuItem[]
    }
    courses: MenuItem[]
    toolkits: MenuItem[]
    certifications: MenuItem[]
    webinars: MenuItem[]
    workshops: MenuItem[]
  }
  code: number
}
