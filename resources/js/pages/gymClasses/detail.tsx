import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { GymClassDetail } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kelas Gym',
        href: '/gym-classes',
    },
    {
        title: 'Jadwal Kelas',
        href: '/gym-classes/schedule',
    }
];

export default function GymClassDetails({ gymClass }: { gymClass: GymClassDetail }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Kelas: ${gymClass.name}`} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                {/* IMAGES */}
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    {gymClass.images && gymClass.images.length > 0 ? (
                        gymClass.images.slice(0, 3).map((image, index) => (
                            <div
                                key={index}
                                className="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border"
                            >
                                <img
                                    src={`/storage/gym_class/${image}`}
                                    alt={`Gambar ${index + 1}`}
                                    className="w-full h-full object-cover"
                                />
                            </div>
                        ))
                    ) : (
                        [...Array(3)].map((_, i) => (
                            <div
                                key={i}
                                className="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border"
                            >
                                <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                            </div>
                        ))
                    )}
                </div>

                {/* CLASS INFO */}
                <div className="border border-sidebar-border/70 dark:border-sidebar-border rounded-xl p-6 space-y-3 bg-white dark:bg-neutral-900">
                    <h1 className="text-2xl font-bold text-gray-800 dark:text-white">{gymClass.name}</h1>
                    <div className="text-sm text-gray-500 dark:text-gray-400"> {gymClass.code}</div>
                    <p className="text-base text-gray-700 dark:text-gray-300">
                        {gymClass.description || 'Tidak ada deskripsi.'}
                    </p>
                    <div className="text-lg font-semibold text-blue-600 dark:text-blue-400">
                        Harga: Rp{gymClass.price.toLocaleString('id-ID')}
                    </div>
                </div>

                {/* SCHEDULES */}
                <div className="border border-sidebar-border/70 dark:border-sidebar-border rounded-xl p-6 bg-white dark:bg-neutral-900">
                    <h2 className="text-xl font-semibold text-gray-800 dark:text-white mb-4">Jadwal Kelas</h2>
                    {gymClass.gymClassSchedules.length > 0 ? (
                        <ul className="space-y-3">
                            {gymClass.gymClassSchedules.map((schedule) => {
                                const dayName = new Date(schedule.date).toLocaleDateString('id-ID', {
                                    weekday: 'long',
                                });

                                return (
                                    <li key={schedule.id} className="text-sm text-gray-700 dark:text-gray-300">
                                        <div className="font-medium">
                                            {dayName}, {schedule.date} | {schedule.start_time} - {schedule.end_time}
                                        </div>
                                        <div className="text-xs text-gray-500 dark:text-gray-400">
                                            Slot: {schedule.slot} &nbsp;|&nbsp; Tersedia: {schedule.available_slot}
                                        </div>
                                    </li>
                                );
                            })}
                        </ul>
                    ) : (
                        <p className="text-gray-500 dark:text-gray-400">Belum ada jadwal untuk kelas ini.</p>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
