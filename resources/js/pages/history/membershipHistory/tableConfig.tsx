import { Button } from '@/components/ui/button';
import { MembershipHistoryFull } from '@/types';
import { ColumnDef } from '@tanstack/react-table';
import { format, parseISO } from 'date-fns';
import { ArrowUpDown } from 'lucide-react';
import { Link } from '@inertiajs/react';

// Submission Column Labels
export const membershipColumnLabels: Record<string, string> = {
    code: 'Kode Riwayat',
    start_date: 'Tanggal Mulai',
    end_date: 'Tanggal Akhir',
    membership_package_name: 'Nama Paket',
    status: 'Status',
    detail: 'Detail',
};

// Submission Columns Definition
export const membershipColumns: ColumnDef<MembershipHistoryFull>[] = [
    {
        header: '#',
        cell: ({ row }) => row.index + 1,
    },
    {
        accessorKey: 'code',
        header: () => <div className="flex w-[5rem] justify-center text-center">Kode Membership</div>,
        cell: ({ row }) => <div className="flex w-[5rem] justify-center text-center capitalize">{row.getValue('code')}</div>,
    },
    {
        accessorKey: 'start_date',
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
                className="flex w-[4rem] justify-center text-center"
            >
                Tanggal Mulai
                <ArrowUpDown />
            </Button>
        ),
        cell: ({ row }) => {
            const testDateRaw = row.getValue('start_date') as string;
            const testDate = parseISO(testDateRaw);
            const formatted = format(testDate, 'dd-MM-yyyy');

            return <div className="flex w-[4rem] justify-center text-center capitalize">{formatted}</div>;
        },
    },
    {
        accessorKey: 'end_date',
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
                className="flex w-[4rem] justify-center text-center"
            >
                Tanggal Akhir
                <ArrowUpDown />
            </Button>
        ),
        cell: ({ row }) => {
            const testDateRaw = row.getValue('end_date') as string;
            const testDate = parseISO(testDateRaw);
            const formatted = format(testDate, 'dd-MM-yyyy');

            return <div className="flex w-[4rem] justify-center text-center capitalize">{formatted}</div>;
        },
    },
    {
        accessorKey: 'status',
        enableColumnFilter: true,
        header: () => <div className="text-center w-[4rem]">Status</div>,
        cell: ({ row }) => {
            const status = row.getValue('status');
            const statusColor = status === 'active' ? 'bg-green-500' : 'bg-yellow-500';

            return (
                <div className="flex w-[4rem] justify-center">
                    <span className={`text-light-base items-center rounded-2xl px-2 py-1 text-center font-medium capitalize md:px-3 ${statusColor}`}>
                        {row.getValue('status')}
                    </span>
                </div>
            );
        },
    },
    {
        accessorFn: row => row.membership_package.name,
        id: 'membership_package_name',
        header: () => (
            <div className="flex w-[5rem] justify-center text-center">
                Paket Membership
            </div>
        ),
        cell: ({ row }) => (
            <div className="flex w-[5rem] justify-center text-center capitalize">
                {row.getValue('membership_package_name')}
            </div>
        ),
    },
    {
        id: 'detail',
        header: () => <div className="flex justify-center text-center">Detail</div>,
        cell: ({ row }) => (
            <div className="flex justify-center">
                <Link href={`/history/submission/${row.original.code}`} className="small-font-size cursor-pointer rounded-full bg-blue-base px-4 py-1 text-center font-medium text-light-base bg-black text-white dark:bg-white dark:text-black">
                    Lihat Detail
                </Link>
            </div>
        ),
    },
];
