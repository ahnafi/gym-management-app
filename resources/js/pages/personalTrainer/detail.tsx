import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, PersonalTrainerDetail } from '@/types';
import { Head } from '@inertiajs/react';
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Personal Trainer',
        href: '/personal-trainers',
    },
    {
        title: 'Detail',
        href: '/personal-trainers/packages',
    }
];

export default function PersonalTrainerDetails( {ptDetail}: { ptDetail: PersonalTrainerDetail }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Personal Trainer - ${ptDetail.nickname}`} />

            <div className="flex flex-col gap-6 p-4">
                {/* Trainer Bio Section */}
                <div className="flex flex-col md:flex-row gap-6">
                    {/* Trainer Image */}
                    <div className="w-full md:w-1/3 max-h-72 overflow-hidden rounded-xl border border-gray-200 dark:border-neutral-700">
                        {ptDetail.images && ptDetail.images.length > 0 ? (
                            <img
                                src={`/storage/personal_trainer/${ptDetail.images[0]}`}
                                alt={ptDetail.nickname}
                                className="object-cover w-full h-full"
                            />
                        ) : (
                            <div className="flex items-center justify-center h-full bg-gray-100 dark:bg-neutral-800 text-gray-400 dark:text-neutral-400">
                                No image available
                            </div>
                        )}
                    </div>

                    {/* Trainer Info */}
                    <div className="flex flex-col justify-center w-full md:w-2/3 space-y-2">
                        <h1 className="text-3xl font-bold text-gray-900 dark:text-white">Coach {ptDetail.nickname}</h1>
                        <p className="text-sm text-gray-500 dark:text-gray-400"> {ptDetail.code}</p>
                        <p className="text-sm text-gray-600 dark:text-gray-300">{ptDetail.description ?? 'No description provided.'}</p>
                    </div>
                </div>

                {/* Package List Section */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {ptDetail.personalTrainerPackages.length > 0 ? (
                        ptDetail.personalTrainerPackages.map((pkg) => (
                            <Card key={pkg.id} className="shadow-md hover:shadow-lg transition-shadow border border-gray-200 dark:border-neutral-700">
                                {pkg.images && pkg.images.length > 0 ? (
                                    <img
                                        src={`/storage/personal_trainer_package/${pkg.images[0]}`}
                                        alt={pkg.name}
                                        className="w-full h-40 object-cover rounded-t-xl"
                                    />
                                ) : (
                                    <div className="w-full h-40 bg-gray-100 dark:bg-neutral-800 text-gray-400 dark:text-neutral-400 flex items-center justify-center rounded-t-xl">
                                        No image available
                                    </div>
                                )}

                                <CardContent className="p-5 space-y-2">
                                    <h2 className="text-lg font-bold text-gray-800 dark:text-white">{pkg.name}</h2>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">{pkg.code}</p>
                                    <p className="text-sm text-gray-600 dark:text-gray-300">{pkg.description ?? 'No description provided.'}</p>

                                    <div className="text-sm text-gray-500 dark:text-gray-400">
                                        <div><strong>Duration:</strong> {pkg.day_duration} day(s)</div>
                                    </div>

                                    <div className="text-lg font-semibold text-blue-600 dark:text-blue-400">
                                        Rp{pkg.price.toLocaleString()}
                                    </div>
                                </CardContent>
                            </Card>
                        ))
                    ) : (
                        <p className="col-span-3 text-center text-gray-500 dark:text-gray-400">No packages available for this trainer.</p>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
