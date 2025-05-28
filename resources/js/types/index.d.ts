import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    role: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export type MembershipPackage = {
    id: number;
    code: string;
    name: string;
    slug: string | null;
    description: string | null;
    duration: number; // Duration in days
    duration_in_months: number; // Duration in months
    price: number;
    status: 'active' | 'inactive';
    images: string[] | null;
    created_at: string;
    updated_at: string;
};

export type GymClass = {
    id: number;
    code: string;
    name: string;
    slug: string;
    description: string | null;
    price: number;
    status: 'active' | 'inactive';
    images: string[] | null;
    created_at: string;
    updated_at: string;
};

export type GymClassSchedule = {
    id: number;
    date: string;          // format: 'YYYY-MM-DD'
    start_time: string;    // format: 'HH:mm:ss'
    end_time: string;      // format: 'HH:mm:ss'
    slot: number;
    created_at: string;    // datetime string
    updated_at: string;    // datetime string
};

export type GymClassDetail = {
    id: number;
    code: string;
    name: string;
    slug: string | null;
    description: string | null;
    price: number;
    images: string[] | null;
    status: 'active' | 'inactive';
    created_at: string;    // datetime string
    updated_at: string;    // datetime string
    gymClassSchedules: GymClassSchedule[];
};

export type PersonalTrainer = {
    id: number;
    code: string;
    nickname: string;
    description: string | null;
    metadata: Record<string, any> | null;
    images: string[] | null;
    user_personal_trainer_id: number;
    deleted_at: string | null;
    created_at: string;
    updated_at: string;
};

export type PersonalTrainerPackage = {
    id: number;
    code: string;
    name: string;
    slug: string;
    description: string | null;
    day_duration: number;
    price: number;
    images: string[] | null;
    status: 'active' | 'inactive';
    personal_trainer_id: number;
    created_at: string;
    updated_at: string;
};

export type PersonalTrainerDetail = {
    id: number;
    code: string;
    nickname: string;
    description: string | null;
    metadata: Record<string, any> | null;
    images: string[] | null;
    user_personal_trainer_id: number;
    deleted_at: string | null;
    created_at: string;
    updated_at: string;
    personalTrainerPackages: PersonalTrainerPackage[];
}



