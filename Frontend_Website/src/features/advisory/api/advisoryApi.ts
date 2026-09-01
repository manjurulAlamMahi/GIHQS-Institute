import { baseApi } from '@/lib/baseApi'
import type { 
  AdvisoryServicesResponse,
  RequestAdvisoryConsultationResponse,
  AdvisoryRequestPayload,
  AdvisoryRequestSubmitResponse
} from '@/types/advisory.types'

export const advisoryApi = baseApi.injectEndpoints({
  endpoints: (builder) => ({
    getAdvisoryServices: builder.query<AdvisoryServicesResponse, void>({
      query: () => '/advisory-services',
    }),
    getRequestAdvisoryConsultation: builder.query<RequestAdvisoryConsultationResponse, void>({
      query: () => '/request-advisory-consultation',
    }),
    submitAdvisoryRequest: builder.mutation<AdvisoryRequestSubmitResponse, AdvisoryRequestPayload>({
      query: (body) => ({
        url: '/advisory-request',
        method: 'POST',
        body,
      }),
    }),
  }),
})

export const { 
  useGetAdvisoryServicesQuery,
  useGetRequestAdvisoryConsultationQuery,
  useSubmitAdvisoryRequestMutation
} = advisoryApi
