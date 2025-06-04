import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { GymClassDetail } from '@/types';
import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Kelas Gym', href: '/gym-classes' },
  { title: 'Jadwal Kelas', href: '/gym-classes/schedule' },
];

export default function GymClassDetails({ gymClass }: { gymClass: GymClassDetail }) {
  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`Kelas: ${gymClass.name}`} />

      {/* Kita set tinggi manual di sini */}
      <div
        className="md:flex bg-white dark:bg-black"
        style={{ height: 'calc(100vh - 80px)' }} // ganti 64px jika tinggi header berbeda
      >
        {/* Left Panel (Images) */}
        <div className="hidden md:flex w-1/2 pl-5">
          {gymClass.images && gymClass.images.length > 0 ? (
            <img
              src={`/storage/gym_class/${gymClass.images[0]}`}
              alt={gymClass.name}
              className="w-full h-full object-cover rounded-3xl"
            />
          ) : (
            <div className="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-neutral-800 text-gray-400">
              Tidak ada gambar
            </div>
          )}
        </div>

        {/* Right Panel */}
        <div className="md:w-1/2 h-full p-10 flex flex-col">
            {/* Info Section (di pojok atas) */}
            <div className="shrink-0 space-y-2">
                <h1 className="text-3xl font-bold text-gray-800 dark:text-white">{gymClass.name}</h1>
                <div className="text-sm text-gray-500 dark:text-gray-400">{gymClass.code}</div>
                <p className="text-base text-gray-700 dark:text-gray-300">
                {gymClass.description || 'Tidak ada deskripsi.'}
                </p>
                <div className="text-lg font-semibold text-blue-600 dark:text-blue-400">
                Harga: Rp{gymClass.price.toLocaleString('id-ID')}
                </div>
            </div>

            {/* Schedule Section (di pojok bawah dan scrollable) */}
            <div className="flex-1 overflow-y-auto mt-6 space-y-4 pr-2">
                <h2 className="text-xl font-semibold text-gray-800 dark:text-white">Jadwal Kelas</h2>
                {gymClass.gymClassSchedules.length > 0 ? (
                <ul className="space-y-4">
                    {gymClass.gymClassSchedules.map((schedule) => {
                    const dayName = new Date(schedule.date).toLocaleDateString('id-ID', {
                        weekday: 'long',
                    });

                    return (
                        <li
                        key={schedule.id}
                        className="border border-gray-200 dark:border-neutral-700 rounded-xl p-4 space-y-2 bg-white dark:bg-neutral-900"
                        >
                        <div className="flex justify-between items-center">
                            <div>
                            <div className="font-medium text-gray-800 dark:text-white">
                                {dayName}, {schedule.date} | {schedule.start_time} - {schedule.end_time}
                            </div>
                            <div className="text-xs text-gray-500 dark:text-gray-400">
                                Slot: {schedule.slot} | Tersedia: {schedule.available_slot}
                            </div>
                            </div>
                            <Button
                            variant="default"
                            className="bg-[#F61501] text-white hover:bg-[#d31300]"
                            onClick={() => alert(`Beli jadwal id: ${schedule.id}`)}
                            >
                            Buy
                            </Button>
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
      </div>
    </AppLayout>
  );
}
