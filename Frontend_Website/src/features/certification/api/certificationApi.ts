import { baseApi } from '@/lib/baseApi'
import type { ApplyCertificationResponse, GetCertificationCataloguesResponse } from '@/types/certification.types'

export const certificationApi = baseApi.injectEndpoints({
  endpoints: (builder) => ({
    applyForCertification: builder.mutation<ApplyCertificationResponse, FormData>({
      query: (body) => ({
        url: '/apply-for-certification',
        method: 'POST',
        body,
      }),
    }),
    getCertificationCatalogues: builder.query<GetCertificationCataloguesResponse, void>({
      query: () => ({
        url: '/certification-catalogues',
        method: 'GET',
      }),
    }),
  }),
})

export const {
  useApplyForCertificationMutation,
  useGetCertificationCataloguesQuery
} = certificationApi
