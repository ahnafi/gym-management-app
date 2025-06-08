import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, PersonalTrainerDetail } from '@/types';
import { Head } from '@inertiajs/react';
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
  {
    title: 'Personal Trainer',
    href: '/personal-trainers',
  },
  {
    title: 'Detail',
    href: '/personal-trainers/packages',
  }
];

export default function PersonalTrainerDetails({ ptDetail }: { ptDetail: PersonalTrainerDetail }) {
  const [selectedPackage, setSelectedPackage] = useState<null | {
    id: number;
    name: string;
    price: number;
  }>(null);

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
                src={`/storage/${ptDetail.images[0]}`}
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
            <p className="text-sm text-gray-500 dark:text-gray-400">{ptDetail.code}</p>
            <p className="text-sm text-gray-600 dark:text-gray-300">{ptDetail.description ?? 'No description provided.'}</p>
          </div>
        </div>

        {/* Package List Section */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          {ptDetail.personalTrainerPackages.length > 0 ? (
            ptDetail.personalTrainerPackages.map((pkg) => (
              <div
                key={pkg.id}
                className="flex flex-col h-full bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow"
              >
                {pkg.images && pkg.images.length > 0 ? (
                  <img
                    src={`/storage/${pkg.images[0]}`}
                    alt={pkg.name}
                    className="w-full h-48 object-cover"
                  />
                ) : (
                  <div className="w-full h-48 bg-gray-100 dark:bg-neutral-800 text-gray-400 dark:text-neutral-400 flex items-center justify-center">
                    No image available
                  </div>
                )}

                <div className="flex flex-col flex-1 p-5">
                  <h2 className="text-lg font-bold text-gray-800 dark:text-white">{pkg.name}</h2>
                  <div className="text-sm text-gray-500 dark:text-gray-400 mb-2">{pkg.code}</div>
                  <p className="text-sm text-gray-600 dark:text-gray-300 flex-grow">{pkg.description ?? 'No description provided.'}</p>

                  <div className="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    <strong>Durasi:</strong> {pkg.day_duration} pertemuan
                  </div>

                  {/* Harga & Button */}
                  <div className="mt-4 flex items-center justify-between">
                    <span className="text-lg font-semibold text-blue-600 dark:text-blue-400">
                      Rp{pkg.price.toLocaleString('id-ID')}
                    </span>
                    <Button
                      variant="default"
                      className="bg-[#F61501] text-white hover:bg-[#d31300]"
                      onClick={() =>
                        setSelectedPackage({
                          id: pkg.id,
                          name: pkg.name,
                          price: pkg.price,
                        })
                      }
                    >
                      Buy
                    </Button>
                  </div>
                </div>
              </div>
            ))
          ) : (
            <p className="col-span-3 text-center text-gray-500 dark:text-gray-400">No packages available for this trainer.</p>
          )}
        </div>
      </div>

      {/* Modal Dialog */}
      {selectedPackage && (
        <AlertDialog open={!!selectedPackage} onOpenChange={(open) => !open && setSelectedPackage(null)}>
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>Konfirmasi Pembelian</AlertDialogTitle>
              <AlertDialogDescription>
                Apakah Anda yakin ingin membeli paket <strong>{selectedPackage.name}</strong> dengan harga{' '}
                <strong>Rp{selectedPackage.price.toLocaleString('id-ID')}</strong>?
              </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
              <AlertDialogCancel>Batal</AlertDialogCancel>
              <AlertDialogAction
                onClick={() => {
                  alert(`Berhasil membeli paket dengan id: ${selectedPackage.id}`);
                  setSelectedPackage(null);
                }}
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
