export interface AdvisoryFeature {
  id: number
  description: string
  advisory_focus_id?: number
}

export interface AdvisoryScopeFeature {
  id: number
  advisory_scope_id: number
  icon: string
  title: string
  description: string
}

export interface AdvisoryDeliverableFeature {
  id: number
  advisory_deliverable_card_id: number
  name: string
}

export interface AdvisoryServicePackageFeature {
  id: number
  advisory_service_id: number
  serial_number: string
  tagline: string
  title: string
  description: string
}

export interface AdvisoryServicesResponse {
  success: boolean
  message: string
  data: {
    advisory_headers: {
      id: number
      title1: string
      title2: string
      tagline: string
      description: string
      content_file: string | null
      injected_status: boolean
    }
    advisory_focuses: {
      id: number
      title: string
      description: string
      advisory_focus_features: AdvisoryFeature[]
    }
    advisory_scopes: {
      id: number
      title1: string
      title2: string
      description: string
      advisory_scope_features: AdvisoryScopeFeature[]
    }
    advisory_deliverable_cards: {
      id: number
      title1: string
      title2: string
      description: string
      advisory_deliverable_card_features: AdvisoryDeliverableFeature[]
    }
    advisory_services: {
      id: number
      title1: string
      title2: string
      description: string
      advisory_service_features: AdvisoryServicePackageFeature[]
    }
    advisory_discuss_cards: {
      id: number
      title1: string
      title2: string
      description: string
      button_text: string
    }
  }
  code: number
}

export interface RequestAdvisoryConsultationResponse {
  success: boolean
  message: string
  data: {
    request_advisories: {
      id: number
      title1: string
      title2: string
      tagline: string
      description: string
    }
  }
  code: number
}

export interface AdvisoryRequestPayload {
  organization_name: string
  full_name: string
  work_email: string
  phone_number: string
  country: string
  organization_type: string
  service_of_interest: string
  desired_timeline: string
  description_of_needs: string
}

export interface AdvisoryRequestSubmitResponse {
  success: boolean
  message: string
  data: AdvisoryRequestPayload & {
    id: number
    status: string
    created_at: string
  }
  code: number
}
