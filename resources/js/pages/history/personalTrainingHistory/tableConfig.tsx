import { Button } from '@/components/ui/button';
import { PersonalTrainingHistory } from '@/types';
import { ColumnDef } from '@tanstack/react-table';
import { format, parseISO } from 'date-fns';
import { ArrowUpDown } from 'lucide-react';

// Submission Column Labels
export const personalTrainingColumnLabels: Record<string, string> = {
    package_code: 'Kode Paket',
    package_name: 'Nama Paket',
    trainer_nickname: 'Trainer',
    start_date: 'Tanggal Mulai',
    end_date: 'Tanggal Berakhir',
    status: 'Status',
    detail: 'Detail',
};

// Submission Columns Definition
export const personalTrainingColumns: ColumnDef<PersonalTrainingHistory>[] = [
    {
        header: "#",
        cell: ({ row }) => row.index + 1,
    },
    {
        accessorFn: row => row.personal_trainer_package?.code ?? "-",
        id: "package_code",
        header: () => <div className="text-center w-[6rem]">Kode Paket</div>,
        cell: ({ row }) => (
            <div className="text-center w-[6rem]">
                {row.getValue("package_code")}
            </div>
        ),
    },
    {
        accessorFn: row => row.personal_trainer_package?.name ?? "-",
        id: "package_name",
        header: () => <div className="text-center w-[8rem]">Nama Paket</div>,
        cell: ({ row }) => (
            <div className="text-center w-[8rem]">
                {row.getValue("package_name")}
            </div>
        ),
    },
    {
        accessorFn: row => row.personal_trainer_package?.personal_trainer?.nickname ?? "-",
        id: "trainer_nickname",
        header: () => <div className="text-center w-[6rem]">Trainer</div>,
        cell: ({ row }) => (
            <div className="text-center w-[6rem] capitalize">
               Coach {row.getValue("trainer_nickname")}
            </div>
        ),
    },
    {
        accessorKey: "start_date",
        header: ({ column }) => (
            <Button
                variant="ghost"
                onClick={() =>
                    column.toggleSorting(column.getIsSorted() === "asc")
                }
                className="text-center w-[6rem]"
            >
                Tanggal Mulai
                <ArrowUpDown className="ml-2 h-4 w-4" />
            </Button>
        ),
        cell: ({ row }) => {
            const date = parseISO(row.getValue("start_date"));
            return (
                <div className="text-center w-[6rem]">
                    {format(date, "dd-MM-yyyy")}
                </div>
            );
        },
    },
    {
        accessorKey: "end_date",
        header: ({ column }) => (
            <Button
                variant="ghost"
                onClick={() =>
                    column.toggleSorting(column.getIsSorted() === "asc")
                }
                className="text-center w-[6rem]"
            >
                Tanggal Berakhir
                <ArrowUpDown className="ml-2 h-4 w-4" />
            </Button>
        ),
        cell: ({ row }) => {
            const date = parseISO(row.getValue("end_date"));
            return (
                <div className="text-center w-[6rem]">
                    {format(date, "dd-MM-yyyy")}
                </div>
            );
        },
    },
    {
        accessorKey: "status",
        header: () => <div className="text-center w-[6rem]">Status</div>,
        cell: ({ row }) => {
            const status = row.getValue("status") as string;

            const statusMapping: Record<string, { label: string; color: string }> = {
                active: { label: "Aktif", color: "bg-blue-500" },
                cancelled: { label: "Dibatalkan", color: "bg-red-500" },
                completed: { label: "Selesai", color: "bg-green-500" },
            };

            const { label, color } = statusMapping[status] || {
                label: status,
                color: "bg-gray-300",
            };

            return (
                <div className="text-center w-[6rem]">
          <span
              className={`rounded-full px-3 py-1 text-white text-sm font-medium ${color}`}
          >
            {label}
          </span>
                </div>
            );
        },
    },
    {
        id: "detail",
        header: () => <div className="text-center">Detail</div>,
        cell: () => (
            <div className="flex justify-center">
                <Button
                    className="small-font-size cursor-pointer rounded-full bg-blue-base px-4 py-1 text-center font-medium text-light-base bg-black text-white dark:bg-white dark:text-black"
                >
                    Lihat Detail
                </Button>
            </div>
        ),
    },
];
