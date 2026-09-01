import { baseApi } from '@/lib/baseApi'
import type { 
  MembershipPackagesResponse,
  SingleMembershipPackageResponse,
  MembershipCheckoutRequest,
  MembershipCheckoutResponse
} from '@/types/membership.types'

export const membershipApi = baseApi.injectEndpoints({
  endpoints: (builder) => ({
    getMembershipPackages: builder.query<MembershipPackagesResponse, void>({
      query: () => '/membership-packages',
    }),
    getMembershipPackageById: builder.query<SingleMembershipPackageResponse, number>({
      query: (id) => `/membership-packages/${id}`,
    }),
    membershipCheckout: builder.mutation<MembershipCheckoutResponse, MembershipCheckoutRequest>({
      query: (body) => ({
        url: '/membership/checkout',
        method: 'POST',
        body,
      }),
    }),
  }),
})

export const { 
  useGetMembershipPackagesQuery,
  useGetMembershipPackageByIdQuery,
  useMembershipCheckoutMutation
} = membershipApi
