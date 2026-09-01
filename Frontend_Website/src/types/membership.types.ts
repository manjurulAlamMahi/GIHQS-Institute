export interface MembershipFeature {
    id: number;
    membership_package_id: number;
    description: string;
    badge: string | null;
    note: string | null;
}

export interface MembershipPackage {
    id: number;
    name: string;
    title: string;
    short_description: string;
    price: number;
    status: number;
    features: MembershipFeature[];
}

export interface MembershipPackagesResponse {
    success: boolean;
    message: string;
    data: {
        membership_packages: MembershipPackage[];
    };
    code: number;
}

export interface SingleMembershipPackageResponse {
    success: boolean;
    message: string;
    data: {
        membership_package: MembershipPackage;
    };
    code: number;
}

export interface MembershipCheckoutRequest {
    membership_package_id: number;
    success_url: string;
    cancel_url: string;
}

export interface MembershipCheckoutResponse {
    success: boolean;
    message: string;
    data: {
        redirect_url: string;
        session_id: string;
        purchase_id: number;
        order_id: string;
        package: {
            id: number;
            name: string;
            price: number;
        };
    };
    code: number;
}
