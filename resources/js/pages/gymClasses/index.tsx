import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { GymClass } from '@/types';
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { Head } from '@inertiajs/react';
import { Link } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kelas Gym',
        href: '/gym-classes',
    },
];

export default function GymClasses({ gymClasses }: { gymClasses: GymClass[] }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kelas Gym" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Daftar Kelas Gym</h1>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {gymClasses.length > 0 ? (
                        gymClasses.map((kelas) => (
                            <Link
                                key={kelas.id}
                                href={route('gym-classes.detail', { gymClass: kelas.slug })}
                                className="block"
                            >
                                <Card className="shadow-sm border border-gray-200 dark:border-neutral-700 hover:shadow-lg transition-shadow rounded-xl overflow-hidden">
                                    {kelas.images && kelas.images.length > 0 ? (
                                        <img
                                            src={`/storage/gym_class/${kelas.images[0]}`}
                                            alt={kelas.name}
                                            className="w-full h-40 object-cover"
                                        />
                                    ) : (
                                        <div className="w-full h-40 bg-gray-100 dark:bg-neutral-800 text-gray-400 dark:text-neutral-400 flex items-center justify-center">
                                            Tidak ada gambar
                                        </div>
                                    )}

                                    <CardContent className="p-5 space-y-2">
                                        <h2 className="text-lg font-bold text-gray-800 dark:text-white">{kelas.name}</h2>
                                        <div className="text-sm text-gray-500 dark:text-gray-400 space-y-1">{kelas.code}</div>
                                        <p className="text-sm text-gray-600 dark:text-gray-300">{kelas.description ?? 'Belum ada deskripsi.'}</p>

                                        <div className="text-lg font-semibold text-blue-600 dark:text-blue-400">
                                            Rp{kelas.price.toLocaleString('id-ID')}
                                        </div>
                                    </CardContent>
                                </Card>
                            </Link>
                        ))
                    ) : (
                        <p className="col-span-3 text-center text-gray-500 dark:text-gray-400">Belum ada kelas gym yang tersedia.</p>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
