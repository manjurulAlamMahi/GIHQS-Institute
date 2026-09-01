import { baseApi } from '@/lib/baseApi'
import type { CataloguesResponse, SingleCatalogueResponse, CheckoutRequest, CheckoutResponse, CertificationCataloguesResponse, MenuCataloguesResponse } from '@/types/catalogue.types'

export const catalogueApi = baseApi.injectEndpoints({
  endpoints: (builder) => ({
    getCatalogues: builder.query<CataloguesResponse, { keyword?: string; sorting?: string; filtering?: string } | void>({
      query: (params) => {
        let url = '/catalogues';
        if (params) {
          const searchParams = new URLSearchParams();
          if (params.keyword) searchParams.append('keyword', params.keyword);
          if (params.sorting && params.sorting !== 'all') searchParams.append('sorting', params.sorting);
          if (params.filtering) searchParams.append('filtering', params.filtering);
          
          const queryString = searchParams.toString();
          if (queryString) {
            url += `?${queryString}`;
          }
        }
        return url;
      },
    }),
    getCatalogueById: builder.query<SingleCatalogueResponse, string | number>({
      query: (id) => `/catalogues/${id}`,
    }),
    createCheckout: builder.mutation<CheckoutResponse, CheckoutRequest>({
      query: (body) => ({
        url: '/checkout',
        method: 'POST',
        body,
      }),
    }),
    getCertificationCatalogues: builder.query<CertificationCataloguesResponse, void>({
      query: () => '/certification-catalogues',
    }),
    getMenuCatalogues: builder.query<MenuCataloguesResponse, { service_type?: string } | void>({
      query: (params) => {
        let url = '/catalogues/menu';
        if (params?.service_type) {
          url += `?service_type=${params.service_type}`;
        }
        return url;
      },
    }),
  }),
})

export const { useGetCataloguesQuery, useGetCatalogueByIdQuery, useCreateCheckoutMutation, useGetCertificationCataloguesQuery, useGetMenuCataloguesQuery } = catalogueApi
