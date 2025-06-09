import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { GymClassDetail } from '@/types';
import { Head, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
  AlertDialog,
  AlertDialogContent,
  AlertDialogHeader,
  AlertDialogFooter,
  AlertDialogTitle,
  AlertDialogDescription,
  AlertDialogCancel,
  AlertDialogAction,
} from '@/components/ui/alert-dialog';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Kelas Gym', href: '/gym-classes' },
  { title: 'Jadwal Kelas', href: '/gym-classes/schedule' },
];

export default function GymClassDetails({ gymClass }: { gymClass: GymClassDetail }) {
  const [selectedSchedule, setSelectedSchedule] = useState<null | {
    id: number;
    name: string;
    schedule: string;
    price: number;
  }>(null);

    const handleCheckout = (gcId: number, gcsId: number) => {
        setSelectedSchedule(null);
        router.post('/payments/checkout', {
            purchasable_type: 'gym_class',
            purchasable_id: gcId,
            gym_class_schedule_id: gcsId,
        });
    };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`Kelas: ${gymClass.name}`} />

      <div
        className="md:flex bg-white dark:bg-black"
        style={{ height: 'calc(100vh - 80px)' }}
      >
        {/* Left Panel */}
        <div className="hidden md:flex w-1/2 pl-5">
          {gymClass.images && gymClass.images.length > 0 ? (
            <img
              src={`/storage/${gymClass.images[0]}`}
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
          {/* Info Section */}
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

          {/* Schedule Section */}
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
                          onClick={() =>
                            setSelectedSchedule({
                              id: schedule.id,
                              name: gymClass.name,
                              schedule: `${dayName}, ${schedule.date} | ${schedule.start_time} - ${schedule.end_time}`,
                              price: gymClass.price,
                            })
                          }
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

      {/* Alert Dialog */}
      {selectedSchedule && (
        <AlertDialog open={!!selectedSchedule} onOpenChange={(open) => !open && setSelectedSchedule(null)}>
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>Konfirmasi Pembelian</AlertDialogTitle>
              <AlertDialogDescription className="text-md">
                Apakah Anda yakin ingin reservasi Kelas Gym "<strong className="text-primary font-semibold">{selectedSchedule.name}</strong>" dengan jadwal <strong className="text-primary font-semibold">{selectedSchedule.schedule}</strong> dengan harga{' '} <strong className="text-primary font-semibold">Rp{selectedSchedule.price.toLocaleString('id-ID')}</strong>?
              </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
              <AlertDialogCancel>Batal</AlertDialogCancel>
              <AlertDialogAction
                onClick={() => handleCheckout( gymClass.id, selectedSchedule.id)}
              >
                Konfirmasi
              </AlertDialogAction>
            </AlertDialogFooter>
          </AlertDialogContent>
        </AlertDialog>
      )}
    </AppLayout>
  );
}
