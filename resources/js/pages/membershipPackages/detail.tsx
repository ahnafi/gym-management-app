    import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
    import AppLayout from '@/layouts/app-layout';
    import { type BreadcrumbItem } from '@/types';
    import { MembershipPackage} from '@/types';
    import { Head } from '@inertiajs/react';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Paket Membership',
            href: '/membership-packages',
        },
        {
            title: 'Detail Paket',
            href: '/membership-packages/detail',
        }
    ];

    export default function MembershipPackages({ mPackage }: { mPackage: MembershipPackage }) {
        return (
            <AppLayout breadcrumbs={breadcrumbs}>
                <Head title={`Paket Membership - ${mPackage.name}`} />
                <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

                    {/* Image gallery */}
                    <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                        {mPackage.images && mPackage.images.length > 0 ? (
                            mPackage.images.map((img, i) => (
                                <div key={i} className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                                    <img
                                        src={`/storage/membership_package/${img}`}
                                        alt={`${mPackage.name} ${i + 1}`}
                                        className="w-full h-full object-cover"
                                    />
                                </div>
                            ))
                        ) : (
                            <div className="col-span-3 text-center text-sm text-gray-500">No images available</div>
                        )}
                    </div>

                    {/* Detail info */}
                    <div className="border border-sidebar-border/70 dark:border-sidebar-border relative rounded-xl p-6 bg-white dark:bg-neutral-900">
                        <h1 className="text-2xl font-bold text-gray-800 dark:text-white">{mPackage.name}</h1>
                        <p className="text-sm text-gray-500 dark:text-gray-400">Kode: {mPackage.code}</p>
                        <p className="text-sm text-gray-600 dark:text-gray-300 mt-4">{mPackage.description}</p>

                        <div className="mt-4 space-y-1">
                            <p className="text-lg font-semibold text-primary">Rp {mPackage.price.toLocaleString()}</p>
                            <p className="text-sm text-gray-500 dark:text-gray-400">Durasi: {mPackage.duration} hari</p>
                            <p className="text-sm text-gray-500 dark:text-gray-400">({mPackage.duration_in_months} bulan)</p>
                        </div>
                    </div>
                </div>
            </AppLayout>
        );
    }
