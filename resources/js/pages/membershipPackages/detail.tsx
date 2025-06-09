import { useState, useEffect } from 'react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { MembershipPackageCatalog } from '@/types';
import { Head } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';
import { toast, ToastContainer } from 'react-toastify';
import { Button } from '@/components/ui/button';
import {
  AlertDialog,
  AlertDialogTrigger,
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
    title: 'Paket Membership',
    href: '/membership-packages',
  },
  {
    title: 'Detail Paket',
    href: '/membership-packages/detail',
  },
];

export default function MembershipPackages({ mPackage }: { mPackage: MembershipPackageCatalog }) {
  const [openDialog, setOpenDialog] = useState(false);
  const { errors } = usePage().props as unknown as { errors: Record<string, string[]> };  // Type this as needed
  const [alertMessage, setAlertMessage] = useState<string | null>(null);

    useEffect(() => {
        if (errors && Object.keys(errors).length > 0) {
            Object.values(errors).forEach((messages) => {
                messages.forEach((message) => {
                    toast.error(message, {
                        position: 'top-center',
                        autoClose: 5000,
                        hideProgressBar: false,
                        closeOnClick: true,
                        pauseOnHover: true,
                        draggable: false,
                        progress: undefined,
                    });
                });
            });
        }
    }, [errors]);

    const handleCheckout = () => {
    router.post('/payments/checkout', {
        purchasable_type: 'membership_package',
        purchasable_id: mPackage.id,
    });
  };


    return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`Paket Membership - ${mPackage.name}`} />

      <section className="bg-white dark:bg-gray-500/20 mx-3 my-3 rounded-xl p-4">
        <div className="grid max-w-screen-xl px-4 py-8 mx-auto gap-8 lg:py-16 lg:grid-cols-12">

          {/* Kiri: Informasi detail */}
          <div className="mr-auto place-self-center lg:col-span-7">
            <h1 className="mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl dark:text-white">
              {mPackage.name}
            </h1>
            <p className="text-sm text-gray-500 dark:text-gray-400 mb-2">Kode: {mPackage.code}</p>
            <p className="mb-6 font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400">
              {mPackage.description}
            </p>
            <p className="text-2xl font-bold text-primary mb-2">Rp {mPackage.price.toLocaleString()}</p>
            <p className="text-base text-gray-500 dark:text-gray-400">Durasi: {mPackage.duration} hari ({mPackage.duration_in_months} bulan)</p>

            <div className="mt-6">
              <AlertDialog open={openDialog} onOpenChange={setOpenDialog}>
                <AlertDialogTrigger asChild>
                  <Button className="bg-[#F61501] hover:bg-[#d31300] text-white">
                    Beli Sekarang
                    <svg className="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path
                        fillRule="evenodd"
                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                        clipRule="evenodd"
                      />
                    </svg>
                  </Button>
                </AlertDialogTrigger>
                <AlertDialogContent>
                  <AlertDialogHeader>
                    <AlertDialogTitle>Konfirmasi Pembelian</AlertDialogTitle>
                    <AlertDialogDescription className="text-md">
                      Apakah Anda yakin ingin membeli Paket Membership "<strong className="text-primary font-semibold">{mPackage.name}</strong>" seharga{' '}
                      <span className="text-primary font-semibold">Rp {mPackage.price.toLocaleString()}</span> untuk {mPackage.duration} hari ({mPackage.duration_in_months} bulan)?
                    </AlertDialogDescription>
                  </AlertDialogHeader>
                  <AlertDialogFooter>
                    <AlertDialogCancel>Batal</AlertDialogCancel>
                    <AlertDialogAction onClick={handleCheckout}>Konfirmasi</AlertDialogAction>
                  </AlertDialogFooter>
                </AlertDialogContent>
              </AlertDialog>
            </div>
          </div>

          <div className="hidden lg:mt-0 lg:col-span-5 lg:flex">
            {mPackage.images && mPackage.images.length > 0 ? (
              <img
                src={`/storage/${mPackage.images[0]}`}
                alt={`${mPackage.name}`}
                className="w-full h-auto rounded-xl object-cover border border-sidebar-border/70 dark:border-sidebar-border"
              />
            ) : (
              <div className="text-sm text-gray-500 dark:text-gray-400">No image available</div>
            )}
          </div>
        </div>
      </section>
    </AppLayout>
  );
}
