import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/react';
import { PersonalTrainer } from '@/types'; // Or your actual type path

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Personal Trainer',
        href: '/personal-trainers',
    },
];

export default function PersonalTrainers({ trainers }: { trainers: PersonalTrainer[] }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Personal Trainer" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    {trainers.length > 0 ? (
                        trainers.map((trainer) => (
                            <Link
                                key={trainer.id}
                                href={route('personal-trainers.package', trainer.slug)}
                                className="bg-white dark:bg-neutral-900 shadow-md rounded-2xl overflow-hidden border border-gray-200 dark:border-neutral-700 hover:shadow-lg transition-shadow block"
                            >
                                {trainer.images && trainer.images.length > 0 ? (
                                    <img
                                        src={`/storage/personal_trainer/${trainer.images[0]}`}
                                        alt={trainer.nickname}
                                        className="w-full h-48 object-cover"
                                    />
                                ) : (
                                    <div className="flex items-center justify-center w-full h-48 bg-gray-100 dark:bg-neutral-800 text-gray-400 dark:text-neutral-400">
                                        No image available
                                    </div>
                                )}

                                <div className="p-6 space-y-2">
                                    <h2 className="text-xl font-bold text-gray-800 dark:text-white">{trainer.nickname}</h2>
                                    <p className="text-sm text-gray-500 dark:text-gray-400"> {trainer.code}</p>
                                    <p className="text-sm text-gray-600 dark:text-gray-300">{trainer.description || 'No description provided.'}</p>
                                </div>
                            </Link>
                        ))
                    ) : (
                        <p className="col-span-3 text-center text-gray-500 dark:text-gray-400">No personal trainers found.</p>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
