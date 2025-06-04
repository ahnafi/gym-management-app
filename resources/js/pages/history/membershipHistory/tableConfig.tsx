import { Button } from '@/components/ui/button';
import { SimpleOption, type SubmissionSchedule, Testing, Transaction } from '@/types';
import { ColumnDef } from '@tanstack/react-table';
import { format, parseISO } from 'date-fns';
import { id } from 'date-fns/locale';
import { ArrowUpDown, Download } from 'lucide-react';
import { Link } from '@inertiajs/react';

// Submission Column Labels
export const submissionColumnLabels: Record<string, string> = {
    code: 'Kode Pengajuan',
    test_submission_date: 'Tanggal',
    company_name: 'Perusahaan',
    lab_code: 'Lab',
    test_name: 'Jenis Pengujian',
    status: 'Status',
    detail: 'Detail',
};

// Submission Status Options
export const submissionStatusOptions: SimpleOption[] = [
    { id: 1, name: 'Approved' },
    { id: 2, name: 'Rejected' },
    { id: 3, name: 'Submitted' },
];

// Transaction Status Options
export const transactionStatusOptions: SimpleOption[] = [
    { id: 1, name: 'Pending' },
    { id: 2, name: 'Success' },
    { id: 3, name: 'Failed' },
];

// Testing Status Options
export const testingStatusOptions: SimpleOption[] = [
    { id: 1, name: 'Testing' },
    { id: 2, name: 'Completed' },
];

// Transaction Payment Method Options
export const paymentMethodOptions: SimpleOption[] = [
    { id: 1, name: 'BANK JATENG' },
    { id: 2, name: 'BANK MANDIRI' },
    { id: 3, name: 'BANK BNI' },
    { id: 4, name: 'BANK BRI' },
    { id: 5, name: 'BANK BSI' },
    { id: 6, name: 'BANK BTN' },
];

// Tranction Column Labels
export const transactionColumnLabels: Record<string, string> = {
    code: 'Kode Transaksi',
    created_at: 'Tanggal Dibuat',
    amount: 'Jumlah',
    payment_invoice_file: 'Invoice',
    status: 'Status Pembayaran',
    detail: 'Detail',
};

// Testing Column Labels
export const testingColumnLabels: Record<string, string> = {
    code: 'Kode Pengujian',
    test_date: 'Tanggal Pengujian',
    status: 'Status Pengujian',
    detail: 'Detail',
};

// Format Rupiah  Function
const formatRupiah = (value: number, currency = 'IDR') => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: currency,
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

// Submission Columns Definition
export const submissionColumns: ColumnDef<SubmissionSchedule>[] = [
    {
        header: '#',
        cell: ({ row }) => row.index + 1,
    },
    {
        accessorKey: 'code',
        header: () => <div className="flex w-[5rem] justify-center text-center">Kode Pengajuan</div>,
        cell: ({ row }) => <div className="flex w-[5rem] justify-center text-center capitalize">{row.getValue('code')}</div>,
    },
    {
        accessorKey: 'test_submission_date',
        enableColumnFilter: true,
        filterFn: (row, columnId, filterValue) => {
            const rowDate = new Date(row.getValue(columnId));
            const start = new Date(filterValue.start);
            const end = filterValue.end ? new Date(filterValue.end) : start;

            start.setHours(0, 0, 0, 0);
            end.setHours(23, 59, 59, 999);
            rowDate.setHours(12, 0, 0, 0);

            return rowDate >= start && rowDate <= end;
        },
        header: ({ column }) => (
            <Button
                variant="ghost"
                onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}
                className="flex w-[5rem] justify-center text-center"
            >
                Tanggal
                <ArrowUpDown />
            </Button>
        ),
        cell: ({ row }) => {
            const testDateRaw = row.getValue('test_submission_date') as string;
            const testDate = parseISO(testDateRaw);
            const formatted = format(testDate, 'dd-MM-yyyy');

            return <div className="flex w-[5rem] justify-center text-center capitalize">{formatted}</div>;
        },
    },
    {
        accessorKey: 'company_name',
        header: () => <div className="w-[5rem]">Perusahaan</div>,
        cell: ({ row }) => <div className="w-[7rem]">{row.getValue('company_name')}</div>,
    },
    {
        accessorKey: 'lab_code',
        enableColumnFilter: true,
        header: () => <div className="flex w-[4rem] justify-center">Lab</div>,
        cell: ({ row }) => <div className="flex w-[4rem] justify-center">{row.getValue('lab_code')}</div>,
    },
    {
        accessorKey: 'test_name',
        enableColumnFilter: true,
        header: () => <div className="text-center">Jenis Pengujian</div>,
        cell: ({ row }) => {
            const test = row.getValue('test_name') as string | null;
            const pkg = row.original.package_name as string | null;
            return <div>{test || pkg || '-'}</div>;
        },
    },
    {
        accessorKey: 'status',
        enableColumnFilter: true,
        header: () => <div className="text-center">Status</div>,
        cell: ({ row }) => {
            const status = row.getValue('status');
            const statusColor = status === 'approved' ? 'bg-green-base' : status === 'rejected' ? 'bg-red-base' : 'bg-yellow-base';

            return (
                <div className="flex w-full justify-center">
                    <span className={`text-light-base items-center rounded-2xl px-2 py-1 text-center font-medium capitalize md:px-3 ${statusColor}`}>
                        {row.getValue('status')}
                    </span>
                </div>
            );
        },
    },
    {
        id: 'detail',
        header: () => <div className="flex justify-center text-center">Detail</div>,
        cell: ({ row }) => (
            <div className="flex justify-center">
                <Link href={`/history/submission/${row.original.code}`} className="small-font-size cursor-pointer rounded-full bg-blue-base px-2 py-1 text-center font-medium text-light-base hover:bg-blue-600">
                    Lihat Detail
                </Link>
            </div>
        ),
    },
];

// Transaction Column Definition
export const transactionColumns: ColumnDef<Transaction>[] = [
    {
        header: '#',
        cell: ({ row }) => row.index + 1,
    },
    {
        accessorKey: 'code',
        header: () => <div className="flex w-[7rem] justify-center text-center">Kode Transaksi</div>,
        cell: ({ row }) => <div className="flex w-[7rem] justify-center text-center capitalize">{row.getValue('code')}</div>,
    },
    {
        accessorKey: 'created_at',
        header: ({column}) => (
            <Button
                variant="ghost"
                onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}
                className="flex w-[5rem] justify-center text-center"
            >
                Tanggal
                <ArrowUpDown />
            </Button>
        ),
        enableColumnFilter: true,
        filterFn: (row, columnId, filterValue) => {
            const rowDate = new Date(row.getValue(columnId));
            const start = new Date(filterValue.start);
            const end = filterValue.end ? new Date(filterValue.end) : start;

            start.setHours(0, 0, 0, 0);
            end.setHours(23, 59, 59, 999);
            rowDate.setHours(12, 0, 0, 0);

            return rowDate >= start && rowDate <= end;
        },
        cell: ({ row }) => {
            const createdAtRaw = row.getValue('created_at') as string;
            const createdAt = parseISO(createdAtRaw);
            const formatted = format(createdAt, 'dd-MM-yyyy, HH:mm', { locale: id });

            return <div className="flex w-[5rem] justify-center text-center capitalize">{formatted}</div>;
        },
    },
    {
        accessorKey: 'amount',
        header: () => <div className="text-center">Jumlah</div>,
        cell: ({ row }) => {
            const amount = row.getValue('amount') as number;
            return <div className="text-center">{formatRupiah(amount)}</div>;
        },
    },
    {
        accessorKey: 'payment_invoice_file',
        header: () => <div className="text-center">Invoice</div>,
        cell: ({ row }) => {
            const invoice = row.getValue('payment_invoice_file') as string | null;

            const handleDownload = () => {
                if (invoice) {
                    const url = `/storage/payment_invoice/${invoice}`;
                    const link = document.createElement('a');
                    link.href = url;
                    link.target = '_blank';
                    link.download = invoice;
                    console.log(url);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            };

            return (
                <div className="flex justify-center">
                    {invoice ? (
                        <Button onClick={handleDownload} className="cursor-pointer gap-1">
                            <Download size={14} />
                            Download
                        </Button>
                    ) : (
                        '-'
                    )}
                </div>
            );
        },
    },
    {
        accessorKey: 'status',
        header: () => <div className="w-full text-center">Status Pembayaran</div>,
        cell: ({ row }) => {
            const status = row.getValue('status') as string;
            const statusColor = status === 'pending' ? 'bg-yellow-base' : status === 'success' ? 'bg-green-base' : 'bg-red-base';

            return (
                <div className="flex w-full justify-center">
                    <div className={`text-light-base items-center rounded-2xl px-2 py-1 text-center font-medium capitalize md:px-3 ${statusColor}`}>{status}</div>
                </div>
            );
        },
    },
    {
        id: 'detail',
        header: () => <div className="flex justify-center text-center">Detail</div>,
        cell: ({ row }) => (
            <div className="flex justify-center">
                <Link href={`/history/transaction/${row.original.code}`} className="small-font-size cursor-pointer rounded-full bg-blue-base px-2 py-1 text-center font-medium text-light-base hover:bg-blue-600">
                    Lihat Detail
                </Link>
            </div>
        ),
    },
];

// Testing Column Definition
export const testingColumns: ColumnDef<Testing>[] = [
    {
        header: '#',
        cell: ({ row }) => row.index + 1,
    },
    {
        accessorKey: 'code',
        header: () => <div className="flex w-[7rem] justify-center text-center">Kode Pengujian</div>,
        cell: ({ row }) => <div className="flex w-[7rem] justify-center text-center capitalize">{row.getValue('code')}</div>,
    },
    {
        accessorKey: 'test_date',
        header: ({ column }) => (
            <Button
                variant="ghost"
                onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}
                className="flex w-[5rem] justify-center text-center"
            >
                Tanggal
                <ArrowUpDown />
            </Button>
        ),
        enableColumnFilter: true,
        filterFn: (row, columnId, filterValue) => {
            const rowDate = new Date(row.getValue(columnId));
            const start = new Date(filterValue.start);
            const end = filterValue.end ? new Date(filterValue.end) : start;

            start.setHours(0, 0, 0, 0);
            end.setHours(23, 59, 59, 999);
            rowDate.setHours(12, 0, 0, 0);

            return rowDate >= start && rowDate <= end;
        },
        cell: ({ row }) => {
            const testDateRaw = row.getValue('test_date') as string;
            const testDate = parseISO(testDateRaw);
            const formatted = format(testDate, 'dd-MM-yyyy');

            return <div className="flex w-[5rem] justify-center text-center capitalize">{formatted}</div>;
        },
    },
    {
        accessorKey: 'status',
        header: () => <div className="text-center">Status Pengujian</div>,
        cell: ({ row }) => {
            const status = row.getValue('status') as string;
            const statusColor = status === 'testing' ? 'bg-yellow-base' : status === 'completed' ? 'bg-green-base' : 'bg-red-base';

            return (
                <div className="flex w-full justify-center">
                    <div className={`text-light-base items-center rounded-2xl px-2 py-1 text-center font-medium capitalize md:px-3 ${statusColor}`}>
                        {row.getValue('status')}
                    </div>
                </div>
            );
        },
    },
    {
        id: 'detail',
        header: () => <div className="flex justify-center text-center">Detail</div>,
        cell: ({ row }) => (
            <div className="flex justify-center">
                <Link href={`/history/test/${row.original.code}`} className="small-font-size cursor-pointer rounded-full bg-blue-base px-2 py-1 text-center font-medium text-light-base hover:bg-blue-600">
                    Lihat Detail
                </Link>
            </div>
        ),
    },
];
