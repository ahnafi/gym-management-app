import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem, SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import {
    LayoutGrid,
    Boxes,
    PersonStanding,
    BicepsFlexed,
    CalendarDays,
    ShoppingCart,
    PanelsTopLeft,
    History,
    CreditCard,
    Dumbbell
} from 'lucide-react';

type NavGroup = {
    label?: string;
    items: NavItem[];
};

const dashboardItem: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
];

const katalogItems: NavItem[] = [
    {
        title: 'Paket Membership',
        href: '/membership-packages',
        icon: Boxes,
    },
    {
        title: 'Personal Trainer',
        href: '/personal-trainers',
        icon: BicepsFlexed,
    },
    {
        title: 'Kelas Gym',
        href: '/gym-classes',
        icon: Dumbbell,
    },
];

const pembayaranItems: NavItem[] = [
    {
        title: 'Pembayaran',
        href: '/payments',
        icon: CreditCard,
    },
];

const riwayatItems: NavItem[] = [
    {
        title: 'Riwayat Membership',
        href: '/membership-history',
        icon: History,
    },
    {
        title: 'Riwayat Kelas Gym',
        href: '/gym-class-history',
        icon: History,
    },
    {
        title: 'Riwayat Personal Training',
        href: '/personal-training-history',
        icon: History,
    },
];

const personalTrainerItems: NavItem[] = [
    {
        title: 'Personal Trainer Dashboard',
        href: '/personal-trainer-dashboard',
        icon: PersonStanding,
    }
];

const adminItems: NavItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin',
        icon: PanelsTopLeft,
    }
];

export function NavMain() {
    const { auth } = usePage<SharedData>().props;
    const user = auth.user;
    const page = usePage();

    const groups: NavGroup[] = [
        { items: dashboardItem }, // Dashboard without label
        { label: 'Katalog', items: katalogItems },
        { label: 'Pembayaran', items: pembayaranItems },
        { label: 'Riwayat', items: riwayatItems },
    ];

    if (user.role === 'trainer') {
        groups.push({ label: 'Personal Trainer', items: personalTrainerItems });
    }

    if (user.role === 'admin') {
        groups.push({ label: 'Dashboard Admin', items: adminItems });
    }

    return (
        <>
            {groups.map(({ label, items }, i) => (
                <SidebarGroup className="px-2 py-0" key={i}>
                    {label && <SidebarGroupLabel>{label}</SidebarGroupLabel>}
                    <SidebarMenu>
                        {items.map((item) => (
                            <SidebarMenuItem key={item.title}>
                                <SidebarMenuButton
                                    asChild
                                    isActive={item.href === page.url}
                                    tooltip={{ children: item.title }}
                                >
                                    <Link href={item.href} prefetch>
                                        {item.icon && <item.icon />}
                                        <span>{item.title}</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        ))}
                    </SidebarMenu>
                </SidebarGroup>
            ))}
        </>
    );
}
