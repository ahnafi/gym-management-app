import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { PaymentHistory, SimpleOption, AlertMessage } from '@/types';
import { ColumnDef } from '@tanstack/react-table';
import { format, parseISO } from 'date-fns';
import { CreditCard, Info, ShoppingCart, X, Box, CalendarDays, Banknote, Code} from 'lucide-react';
import { router } from '@inertiajs/react';

export const paymentColumnLabels: Record<string, string> = {
    code: 'Kode Pembayaran',
    created_at: 'Tanggal Dibuat',
    purchasable_type: 'Tipe Pembelian',
    purchasable_name: 'Nama Produk',
    amount: 'Nominal',
    payment_status: 'Status',
    detail: 'Detail',
};

export const paymentStatusOptions: SimpleOption[] = [
    { id: 1, name: 'Pending' },
    { id: 2, name: 'Paid' },
    { id: 3, name: 'Failed' },
];

export const purchasableTypeOptions: SimpleOption[] = [
    { id: 1, name: 'Paket Membership' },
    { id: 2, name: 'Kelas Gym' },
    { id: 3, name: 'Paket Personal Trainer' }
];

export const getPaymentColumns = (
    setAlertMessage: (message: AlertMessage) => void
): ColumnDef<PaymentHistory>[] => {
    const DetailCell = ({ row }: { row: { original: PaymentHistory } }) => {
        const [openModal, setOpenModal] = useState(false);
        const payment = row.original;

        const handlePayClick = () => {
            if (!payment.snap_token) {
                alert('No snap token available for payment.');
                return;
            }

            window.snap.pay(payment.snap_token, {
                onSuccess: (result) => {
                    router.post('/payments/update-status', {
                        transaction_id: payment.id,
                        status: 'paid',
                    });

                    setAlertMessage({
                        message: "Pembayaran berhasil!",
                        type: 'success',
                    })
                },
                onPending: (result) => {
                    router.post('/payments/update-status', {
                        transaction_id: payment.id,
                        status: 'pending',
                    });


                },
                onError: (result) => {
                    router.post('/payments/update-status', {
                        transaction_id: payment.id,
                        status: 'failed',
                    });

                    setAlertMessage({
                        message: "Pembayaran Gagal",
                        type: 'error',
                    })
                },
            });
        };

        return (
            <>
                <div className="flex justify-center w-[6rem]">
                    <Button variant="outline" size="sm" onClick={() => setOpenModal(true)}>
                        Detail
                    </Button>
                </div>

                {openModal && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center py-8 bg-black/50 dark:bg-black/70 backdrop-blur-sm">
                        <div className="relative bg-white dark:bg-zinc-900 p-6 rounded-2xl  shadow-2xl max-w-md w-full text-gray-800 dark:text-gray-100">
                            <button
                                onClick={() => setOpenModal(false)}
                                className="absolute top-3 right-3 text-gray-500 hover:text-red-500 transition"
                            >
                                <X className="w-5 h-5" />
                            </button>

                            <h2 className="text-xl font-semibold mb-6 flex items-center gap-2">
                                <Info className="w-5 h-5" /> Detail Transaksi
                            </h2>

                            <div className="text-sm space-y-4">
                                <div className="flex items-start gap-2">
                                    <CreditCard className="w-4 h-4 mt-1" />
                                    <span><strong>Kode Pembayaran:</strong> {payment.code}</span>
                                </div>

                                <div className="flex items-start gap-2">
                                    <Banknote className="w-4 h-4 mt-1" />
                                    <span><strong>Nominal:</strong> Rp {payment.amount.toLocaleString()}</span>
                                </div>

                                <div className="flex items-start gap-2">
                                    <Info className="w-4 h-4 mt-1" />
                                    <span>
                                        <strong>Status Pembayaran:</strong>{' '}
                                            {(
                                                {
                                                    pending: 'Pending',
                                                    paid: 'Berhasil Terbayar',
                                                    failed: 'Gagal',
                                                } as Record<string, string>
                                            )[payment.payment_status] ?? payment.payment_status}
                                   </span>
                                </div>


                                <div className="flex items-start gap-2">
                                    <ShoppingCart className="w-4 h-4 mt-1" />
                                    <span>
                                    <strong>Tipe Pembelian:</strong>{' '}
                                        {{
                                            membership_package: 'Paket Membership',
                                            gym_class: 'Gym Class',
                                            personal_trainer_package: 'Paket Personal Trainer',
                                        }[payment.purchasable_type] ?? payment.purchasable_type}
                                </span>
                                </div>

                                <div className="flex items-start gap-2">
                                    <Box className="w-4 h-4 mt-1" />
                                    <span><strong>Nama Produk:</strong> {payment.purchasable_name}</span>
                                </div>

                                <div className="flex items-start gap-2">
                                    <Code className="w-4 h-4 mt-1" />
                                    <span><strong>Kode Produk:</strong> {payment.purchasable_code}</span>
                                </div>

                                {payment.gym_class_schedule && (
                                    <div className="space-y-1 pl-6 border-l-2 border-gray-300 dark:border-gray-600 ml-2">
                                        <strong>Jadwal Kelas Gym:</strong>
                                        <ul className="list-disc ml-4">
                                            <li>Tanggal: {payment.gym_class_schedule.date}</li>
                                            <li>Mulai: {payment.gym_class_schedule.start_time}</li>
                                            <li>Selesai: {payment.gym_class_schedule.end_time}</li>
                                        </ul>
                                    </div>
                                )}

                                <div className="flex items-start gap-2">
                                    <CalendarDays className="w-4 h-4 mt-1" />
                                    <span>
                                    <strong>Tanggal Pembayaran:</strong>{' '}
                                        {payment.payment_date
                                            ? format(parseISO(payment.payment_date), 'HH:mm:ss dd-MM-yyyy')
                                            : '-'}
                                </span>
                                </div>
                            </div>

                            <div className="mt-6 flex flex-col gap-2">
                                {payment.snap_token && (
                                    <Button variant="default" onClick={handlePayClick}>
                                        Bayar Sekarang
                                    </Button>
                                )}
                                <Button variant="outline" onClick={() => setOpenModal(false)}>
                                    Tutup
                                </Button>
                            </div>
                        </div>
                    </div>
                )}
            </>
        );
    };


    return [
        {
            header: '#',
            cell: ({ row }) => row.index + 1,
        },
        {
            accessorKey: 'code',
            header: () => <div className="text-center w-[6rem]">Kode Pembayaran</div>,
            cell: ({ row }) => (
                <div className="text-center w-[6rem] truncate">{row.getValue('code')}</div>
            ),
        },
        {
            accessorKey: 'created_at',
            header: () => <div className="text-center w-[8rem]">Tanggal Dibuat</div>,
            cell: ({ row }) => {
                const dateRaw = row.getValue('created_at') as string | null;
                if (!dateRaw) return <div className="text-center w-[8rem]">-</div>;

                const date = parseISO(dateRaw);
                const formatted = format(date, 'dd-MM-yyyy');
                return <div className="text-center w-[8rem]">{formatted}</div>;
            },
        },
        {
            accessorKey: 'purchasable_type',
            header: () => <div className="text-center w-[8rem]">Tipe Pembelian</div>,
            cell: ({ row }) => {
                const type = row.getValue('purchasable_type') as
                    | 'membership_package'
                    | 'gym_class'
                    | 'personal_trainer_package';

                const mapping: Record<
                    'membership_package' | 'gym_class' | 'personal_trainer_package',
                    string
                > = {
                    membership_package: 'Paket Membership',
                    gym_class: 'Gym Class',
                    personal_trainer_package: 'Paket Personal Trainer',
                };

                return (
                    <div className="capitalize text-center w-[8rem]">
                        {mapping[type] ?? type}
                    </div>
                );
            },
        },
        {
            accessorKey: 'purchasable_name',
            header: () => <div className="text-center w-[12rem]">Nama Produk</div>,
            cell: ({ row }) => (
                <div className="capitalize text-center w-[12rem] truncate">{row.getValue('purchasable_name')}</div>
            ),
        },
        {
            accessorKey: 'amount',
            header: () => <div className="text-center w-[6rem]">Nominal</div>,
            cell: ({ row }) => {
                const amount = row.getValue('amount') as number;
                return (
                    <div className="text-center w-[6rem]">
                        Rp {amount.toLocaleString()}
                    </div>
                );
            },
        },
        {
            accessorKey: 'payment_status',
            header: () => <div className="text-center w-[6rem]">Status</div>,
            cell: ({ row }) => {
                const status = row.getValue('payment_status') as string;
                const statusColor =
                    status === 'paid' ? 'bg-green-500' :
                        status === 'pending' ? 'bg-yellow-500' :
                            'bg-red-500';

                const statusText = {
                    pending: 'Pending',
                    paid: 'Terbayar',
                    failed: 'Gagal',
                }[status.toLowerCase()] ?? status;

                return (
                    <div className="flex justify-center w-[6rem]">
        <span className={`rounded-2xl px-2 py-1 text-white font-medium capitalize ${statusColor}`}>
          {statusText}
        </span>
                    </div>
                );
            },
        },
        {
            id: 'detail',
            header: () => <div className="text-center w-[6rem]">Detail</div>,
            cell: DetailCell,
        },
    ];
}
