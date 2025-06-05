import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { GymClass } from '@/types';
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
                                <div className="flex flex-col h-full bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow">
                                    {/* Gambar di paling atas tanpa gap */}
                                    {kelas.images && kelas.images.length > 0 ? (
                                        <img
                                            src={`/storage/gym_class/${kelas.images[0]}`}
                                            alt={kelas.name}
                                            className="w-full h-48 object-cover"
                                        />
                                    ) : (
                                        <div className="w-full h-48 bg-gray-100 dark:bg-neutral-800 text-gray-400 dark:text-neutral-400 flex items-center justify-center">
                                            Tidak ada gambar
                                        </div>
                                    )}

                                    {/* Konten fleksibel */}
                                    <div className="flex flex-col flex-1 p-5">
                                        <h2 className="text-lg font-bold text-gray-800 dark:text-white">{kelas.name}</h2>
                                        <div className="text-sm text-gray-500 dark:text-gray-400 mb-2">{kelas.code}</div>
                                        <p className="text-sm text-gray-600 dark:text-gray-300 flex-grow">
                                            {kelas.description ?? 'Belum ada deskripsi.'}
                                        </p>
                                        {/* Harga selalu di bawah */}
                                        <div className="text-lg font-semibold text-blue-600 dark:text-blue-400 mt-4">
                                            Rp{kelas.price.toLocaleString('id-ID')}
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        ))
                    ) : (
                        <p className="col-span-3 text-center text-gray-500 dark:text-gray-400">
                            Belum ada kelas gym yang tersedia.
                        </p>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
