export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};

export type Address = {
    id: number;
    label: string;
    line_1: string;
    line_2: string;
    city: string;
    state: string;
    postal_code: string;
    country: string;
};
