import { Button } from '@/components/ui/button';
import { GymClassHistory } from '@/types';
import { ColumnDef } from '@tanstack/react-table';
import { format, parseISO } from 'date-fns';
import { id } from 'date-fns/locale';
import { ArrowUpDown } from 'lucide-react';
import { Link } from '@inertiajs/react';

// Gym Class Column Labels
export const gymClassColumnLabels: Record<string, string> = {
    date: 'Tanggal',
    start_time: 'Waktu Mulai',
    end_time: 'Waktu Akhir',
    class_name: 'Nama Kelas',
    details: 'Detail'
};

// Gym Class History Columns Definition
export const gymClassColumns: ColumnDef<GymClassHistory>[] = [
    {
        header: '#',
        cell: ({ row }) => row.index + 1,
    },
    {
        accessorFn: row => row.gym_class_schedule.date,
        id: 'date',
        enableColumnFilter: true,
        header: ({ column }) => (
            <Button
                variant="ghost"
                onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}
                className="flex w-[6rem] justify-center text-center"
            >
                Tanggal
                <ArrowUpDown className="ml-2 h-4 w-4" />
            </Button>
        ),
        cell: ({ row }) => {
            const rawDate = row.getValue('date') as string;
            const parsed = parseISO(rawDate);
            const formatted = format(parsed, 'dd-MM-yyyy');

            return <div className="flex w-[6rem] justify-center text-center">{formatted}</div>;
        },
    },
    {
        accessorFn: row => row.gym_class_schedule.start_time,
        id: 'start_time',
        header: () => <div className="flex w-[5rem] justify-center text-center">Waktu Mulai</div>,
        cell: ({ row }) => (
            <div className="flex w-[5rem] justify-center text-center">
                {row.getValue('start_time')}
            </div>
        ),
    },
    {
        accessorFn: row => row.gym_class_schedule.end_time,
        id: 'end_time',
        header: () => <div className="flex w-[5rem] justify-center text-center">Waktu Selesai</div>,
        cell: ({ row }) => (
            <div className="flex w-[5rem] justify-center text-center">
                {row.getValue('end_time')}
            </div>
        ),
    },
    {
        accessorFn: row => row.gym_class_schedule.gym_class.name,
        id: 'class_name',
        header: () => <div className="flex w-[10rem] justify-center text-center">Nama Kelas</div>,
        cell: ({ row }) => (
            <div className="flex w-[10rem] justify-center text-center capitalize">
                {row.getValue('class_name')}
            </div>
        ),
    },
    {
        accessorKey: 'id',
        id: 'details',
        header: () => <div className="flex w-[5rem] justify-center text-center">Aksi</div>,
        cell: () => (
            <div className="flex w-[5rem] justify-center text-center">
                <Button className="small-font-size cursor-pointer rounded-full bg-blue-base px-4 py-1 text-center font-medium text-light-base bg-black text-white dark:bg-white dark:text-black">
                    Lihat Detail
                </Button>
            </div>
        ),
    },
];
