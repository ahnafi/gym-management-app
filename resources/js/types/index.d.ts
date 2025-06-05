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

export type MembershipPackageCatalog = {
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

export type PurchasableItem = {
    id: number;
    code: string;
    name: string;
};

export type Transaction = {
    id: number;
    code: string;
    user_id: number;
    amount: number;
    payment_status: string;
    payment_date: string | null;
    gym_class_date: string | null;
    snap_token: string;
    created_at: string;
    updated_at: string;
    purchasable_type: 'membership_package' | 'gym_class' | 'personal_trainer_package';
    purchasable: PurchasableItem;
};

export interface SimpleOption {
    id: number;
    name: string;
}

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
    available_slot: number;
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
    slug: string;
    description: string | null;
    metadata: Record<string> | null;
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
    slug: string;
    description: string | null;
    metadata: Record<string> | null;
    images: string[] | null;
    user_personal_trainer_id: number;
    deleted_at: string | null;
    created_at: string;
    updated_at: string;
    personalTrainerPackages: PersonalTrainerPackage[];
}

export type MembershipPackageHistory = {
    id: number;
    code: string | null;
    name: string;
    slug: string | null;
    description: string | null;
    duration: number;
    price: number;
    status: 'active' | 'expired';
    images: string | null;
    created_at: string;
    updated_at: string;
};

export type MembershipHistory = {
    id: number;
    code: string | null;
    start_date: string;
    end_date: string;
    status: 'active' | 'expired';
    user_id: number;
    membership_package_id: number;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    membership_package: MembershipPackageHistory;
};

export interface GymClassHistory {
    id: number;
    user_id: number;
    status: 'assigned' | 'attended' | 'missed';
    gym_class_schedule_id: number;
    created_at: string; // ISO date string
    gym_class_schedule: {
        id: number;
        date: string; // ISO string: "2025-06-04T17:00:00.000000Z"
        start_time: string; // "08:00:00"
        end_time: string;   // "09:00:00"
        gym_class_id: number;
        gym_class: {
            id: number;
            code: string;
            name: string;
            images: string[]; // Assuming this is a JSON array of image filenames/URLs
        };
    };
}

export interface PersonalTrainingHistory {
    id: number;
    day_left: number;
    start_date: string;
    end_date: string;
    status: 'active' | 'cancelled' | 'completed';
    user_id: number;
    personal_trainer_id: number;
    personal_trainer_package_id: number;
    created_at: string;
    updated_at: string;
    personal_trainer_schedules: {
        id: number;
        scheduled_at: string;
        check_in_time: string;
        check_out_time: string;
        personal_trainer_assignment_id: number;
    }[];
    personal_trainer_package: {
        id: number;
        code: string;
        name: string;
        day_duration: number;
        images: string[];
        personal_trainer_id: number;
        personal_trainer: {
            id: number;
            code: string;
            nickname: string;
            images: string[];
        };
    };
}

