('use-client');

import { DatePicker } from '@/components/DatePicker';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import SearchableSelect from '@/components/ui/SearchableSelect';
import { Calendar } from "@/components/ui/calendar"
import { DayPicker } from 'react-day-picker';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import {
    type BreadcrumbItem,
    MembershipHistory,
    SimpleOption,
} from '@/types';
import { Head } from '@inertiajs/react';
import type { Table as TanStackTable } from '@tanstack/react-table';
import {
    ColumnFiltersState,
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    SortingState,
    useReactTable,
    VisibilityState,
} from '@tanstack/react-table';
import { ChevronDown, Boxes, X } from 'lucide-react';
import * as React from 'react';
import { useEffect, useState } from 'react';
import { toast, ToastContainer } from 'react-toastify';
import { membershipColumnLabels, membershipColumns } from './tableConfig';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Riwayat Membership',
        href: '/gym-class-history',
    },
];

export default function MembershipHistories({
                                          membershipHistories,
                                          membershipPackages,
                                      }: {
    membershipHistories: MembershipHistory[];
    membershipPackages: SimpleOption[];
}) {
    // Submission Table State
    const [membershipSorting, setMembershipSorting] = useState<SortingState>([]);
    const [membershipFilters, setMembershipFilters] = useState<ColumnFiltersState>([]);
    const [membershipVisibility, setMembershipVisibility] = useState<VisibilityState>({});
    const [membershipSelection, setMembershipSelection] = useState({});
    const [membershipRows, setMembershipRows] = useState<number>(10);

    // Submission Table Filter State
    const [membershipSelectedPackage, setMembershipSelectedPackage] = useState<SimpleOption | null>(null);

    const [membershipInitialDate, setMembershipInitialDate] = useState<Date | undefined>();
    const [membershipFinalDate, setMembershipFinalDate] = useState<Date | undefined>();
    const [membershipFinalDateKey, setMembershipFinalDateKey] = useState<number>(Date.now());

    // Alert State
    const [alertMessage, setAlertMessage] = useState<string | null>(null);

    // Initial Date Select Handlers
    const handleInitialDateSelect = (
        date: Date | undefined,
        setInitialDate: (date: Date | undefined) => void,
        setFinalDate: (date: Date | undefined) => void,
        finalDate: Date | undefined,
    ) => {
        const selected = date ?? new Date();
        setInitialDate(selected);

        if (!finalDate || (date && finalDate.getTime() === selected.getTime())) {
            setFinalDate(selected);
        } else if (selected.getTime() > finalDate.getTime()) {
            setAlertMessage('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
            setFinalDate(selected);
        } else {
            setAlertMessage(null);
        }
    };

    // Final Date Select Handlers
    const handleFinalDateSelect = (
        date: Date | undefined,
        initialDate: Date | undefined,
        setInitialDate: (date: Date | undefined) => void,
        setFinalDate: (date: Date | undefined) => void,
        setAlertMessage: (msg: string | null) => void,
        setFinalDateKey: (key: number) => void,
    ) => {
        if (!initialDate || !date) {
            setFinalDate(date);
            return;
        }

        if (!initialDate) {
            setInitialDate(date);
            setFinalDate(date);
            setAlertMessage(null);
            return;
        }

        if (date.getTime() === initialDate.getTime()) {
            setFinalDate(date);
        } else if (date.getTime() < initialDate.getTime()) {
            setAlertMessage('Tanggal akhir tidak boleh lebih kecil dari tanggal awal');
            setFinalDate(initialDate);
            setFinalDateKey(Date.now());
        } else {
            setFinalDate(date);
            setAlertMessage(null);
        }
    };

    const membershipTable = useReactTable<MembershipHistory>({
        data: membershipHistories,
        columns: membershipColumns,
        onSortingChange: setMembershipSorting,
        onColumnFiltersChange: setMembershipFilters,
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        onColumnVisibilityChange: setMembershipVisibility,
        onRowSelectionChange: setMembershipSelection,
        state: {
            sorting: membershipSorting,
            columnFilters: membershipFilters,
            columnVisibility: membershipVisibility,
            rowSelection: membershipSelection,
        },
    });

    // Column Filter Update
    const updateColumnFilter = (setFilters: React.Dispatch<React.SetStateAction<ColumnFiltersState>>, columnId: string, value: unknown) => {
        setFilters((prevFilters) => {
            const otherFilters = prevFilters.filter((f) => f.id !== columnId);
            if (value === undefined || value === null || value === '') {
                return otherFilters;
            }
            return [...otherFilters, { id: columnId, value }];
        });
    };

    // Submission Date Column Filter Effect
    useEffect(() => {
        if (membershipInitialDate) {
            updateColumnFilter(setMembershipFilters, 'start_date', {
                start: membershipInitialDate,
                end: membershipFinalDate ?? membershipInitialDate,
            });
        } else {
            updateColumnFilter(setMembershipFilters, 'start_date', undefined);
        }
    }, [membershipInitialDate, membershipFinalDate]);

    // Reusable Column Filter Effect
    // const useColumnFilterEffect = (
    //     selectedOption: SimpleOption | null,
    //     setFilters: React.Dispatch<React.SetStateAction<ColumnFiltersState>>,
    //     columnId: string,
    // ) => {
    //     useEffect(() => {
    //         if (selectedOption?.name) {
    //             updateColumnFilter(setFilters, columnId, selectedOption.name);
    //         } else {
    //             updateColumnFilter(setFilters, columnId, undefined);
    //         }
    //     }, [selectedOption, columnId, setFilters]);
    // };

    // Submission Lab Column Filter Effect
    useEffect(() => {
        if (membershipSelectedPackage?.name) {
            updateColumnFilter(setMembershipFilters, 'MembershipPackageHistory.name', membershipSelectedPackage.name);
        } else {
            updateColumnFilter(setMembershipFilters, 'MembershipPackageHistory.name', undefined);
        }
    }, [membershipSelectedPackage]);

    // Row Pagination Effect
    const usePageSizeEffect = <T,>(table: TanStackTable<T>, rows: number) => {
        useEffect(() => {
            table.setPageSize(rows);
        }, [rows, table]);
    };

    // Submission Table Row Pagination Effect
    usePageSizeEffect(membershipTable, membershipRows);

    // Alert Message
    useEffect(() => {
        if (alertMessage) {
            toast.error(alertMessage, {
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined,
            });
            setAlertMessage(null);
        }
    }, [alertMessage]);

    // Filter Dialog State
    const [isFilterDialogOpen, setIsFilterDialogOpen] = useState(false);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Riwayat Membership" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-hidden rounded-xl p-4">
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div className="submission col-span-full space-y-2">
                        <h1 className="title font-semibold">Daftar Pengajuan</h1>
                        <div className="membership-table-filters small-font-size mb-2 flex hidden justify-end gap-4 lg:mb-4 lg:flex lg:flex-wrap">
                            {/* Daftar filter untuk layar besar */}
                            <div className="test-type">
                                <SearchableSelect
                                    label="Jenis Pengujian"
                                    options={membershipPackages}
                                    selectedOption={membershipSelectedPackage}
                                    setSelectedOption={setMembershipSelectedPackage}
                                    placeholder="Filter Jenis Membership..."
                                    searchIcon={<Boxes size={16} />}
                                />
                            </div>
                            <div className="date-range-picker">
                                <label className="text-foreground font-medium">Tanggal</label>
                                <div className="flex gap-3">
                                    <div className="initial-date">
                                        <DatePicker
                                            value={membershipInitialDate}
                                            placeholder="Pilih Tanggal Awal"
                                            onDateSelect={(date) =>
                                                handleInitialDateSelect(date, setMembershipInitialDate, setMembershipFinalDate, membershipFinalDate)
                                            }
                                        />
                                    </div>
                                    <div className="flex items-center justify-center">-</div>
                                    <div className="final-date">
                                        <DatePicker
                                            key={membershipFinalDateKey}
                                            value={membershipInitialDate}
                                            placeholder="Pilih Tanggal Akhir"
                                            onDateSelect={(date) =>
                                                handleFinalDateSelect(
                                                    date,
                                                    membershipInitialDate,
                                                    setMembershipInitialDate,
                                                    setMembershipFinalDate,
                                                    setAlertMessage,
                                                    setMembershipFinalDateKey,
                                                )
                                            }
                                        />
                                    </div>
                                </div>


                                {(membershipInitialDate || membershipFinalDate) && (
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setMembershipInitialDate(undefined);
                                            setMembershipFinalDate(undefined);
                                            setMembershipFilters((prev) => prev.filter((f) => f.id !== 'test_submission_date'));
                                        }}
                                        className="text-muted-foreground hover:text-foreground mt-1 flex items-center gap-1"
                                    >
                                        <X size={14} />
                                        Kosongkan pilihan
                                    </button>
                                )}
                            </div>
                        </div>



                        {/* Tombol Filter untuk layar kecil */}
                        <div className="mb-2 flex justify-end lg:mb-4 lg:hidden">
                            <Dialog open={isFilterDialogOpen} onOpenChange={setIsFilterDialogOpen}>
                                <DialogTrigger asChild>
                                    <Button variant="outline" className="bg-blue-base text-light-base small-font-size font-bold">
                                        Filter
                                    </Button>
                                </DialogTrigger>
                                <DialogContent className="animate-slide-up w-fit p-4 md:p-6 lg:p-8">
                                    <DialogHeader>
                                        <DialogTitle>Filter</DialogTitle>
                                    </DialogHeader>
                                    <div className="small-font-size flex flex-col gap-4">
                                        <div className="date-range-picker">
                                            <label className="text-foreground font-medium">Tanggal</label>
                                            <div className="flex justify-between gap-2">
                                                <div className="initial-date flex flex-col">
                                                    <DatePicker
                                                        value={membershipInitialDate}
                                                        placeholder="Pilih Tanggal Awal"
                                                        onDateSelect={(date) =>
                                                            handleInitialDateSelect(
                                                                date,
                                                                setMembershipInitialDate,
                                                                setMembershipFinalDate,
                                                                membershipFinalDate,
                                                            )
                                                        }
                                                    />
                                                </div>
                                                <div className="final-date flex flex-col">
                                                    <DatePicker
                                                        key={membershipFinalDateKey}
                                                        value={membershipInitialDate}
                                                        placeholder="Pilih Tanggal Akhir"
                                                        onDateSelect={(date) =>
                                                            handleFinalDateSelect(
                                                                date,
                                                                membershipInitialDate,
                                                                setMembershipInitialDate,
                                                                setMembershipFinalDate,
                                                                setAlertMessage,
                                                                setMembershipFinalDateKey,
                                                            )
                                                        }
                                                    />
                                                </div>
                                            </div>
                                            {(membershipInitialDate || membershipFinalDate) && (
                                                <button
                                                    type="button"
                                                    onClick={() => {
                                                        setMembershipInitialDate(undefined);
                                                        setMembershipFinalDate(undefined);
                                                        setMembershipFilters((prev) => prev.filter((f) => f.id !== 'start_date'));
                                                    }}
                                                    className="text-muted-foreground hover:text-foreground mt-1 flex items-center gap-1"
                                                >
                                                    <X size={14} />
                                                    Kosongkan pilihan
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                </DialogContent>
                            </Dialog>
                        </div>
                        <div className="submission-table-main">
                            <div className="submission-table-option mb-2 flex justify-between lg:mb-4">
                                <div className="flex w-full justify-end gap-2 flex-wrap">
                                    <div className="code-search flex flex-col">
                                        <Input
                                            placeholder="Cari Kode Membership..."
                                            value={(membershipTable.getColumn('code')?.getFilterValue() as string) ?? ''}
                                            onChange={(e) => membershipTable.getColumn('code')?.setFilterValue(e.target.value)}
                                            className="border-muted bg-background text-foreground focus:ring-primary small-font-size small-font-size w-full rounded-md border py-2 shadow-sm focus:ring-1 focus:outline-none"
                                        />
                                    </div>
                                    <div className="table-column-filter mb-2">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger asChild>
                                                <Button variant="outline" className="small-font-size ml-auto font-normal">
                                                    Kolom <ChevronDown />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                {membershipTable
                                                    .getAllColumns()
                                                    .filter((column) => column.getCanHide())
                                                    .map((column) => {
                                                        return (
                                                            <DropdownMenuCheckboxItem
                                                                key={column.id}
                                                                checked={column.getIsVisible()}
                                                                onCheckedChange={(value) => column.toggleVisibility(!!value)}
                                                                className="small-font-size"
                                                            >
                                                                {membershipColumnLabels[column.id] ?? column.id}
                                                            </DropdownMenuCheckboxItem>
                                                        );
                                                    })}
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </div>
                                    <div className="pagination-rows-selector mb-2">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger asChild>
                                                <Button variant="outline" className="small-font-size ml-auto font-normal">
                                                    Tampilkan {membershipRows} Baris <ChevronDown className="ml-1 h-4 w-4" />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                {[10, 25, 50, 100].map((size) => (
                                                    <DropdownMenuCheckboxItem
                                                        key={size}
                                                        checked={membershipRows === size}
                                                        onCheckedChange={() => setMembershipRows(size)}
                                                        className="small-font-size"
                                                    >
                                                        {size} baris
                                                    </DropdownMenuCheckboxItem>
                                                ))}
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </div>
                                </div>
                            </div>
                            <div className="submission-table-body">
                                <div className="rounded-md border">
                                    <Table className="small-font-size">
                                        <TableHeader>
                                            {membershipTable.getHeaderGroups().map((headerGroup) => (
                                                <TableRow key={headerGroup.id}>
                                                    {headerGroup.headers.map((header) => {
                                                        return (
                                                            <TableHead key={header.id}>
                                                                {header.isPlaceholder
                                                                    ? null
                                                                    : flexRender(header.column.columnDef.header, header.getContext())}
                                                            </TableHead>
                                                        );
                                                    })}
                                                </TableRow>
                                            ))}
                                        </TableHeader>
                                        <TableBody>
                                            {membershipTable.getRowModel().rows?.length ? (
                                                membershipTable.getRowModel().rows.map((row) => (
                                                    <TableRow key={row.id}>
                                                        {row.getVisibleCells().map((cell) => (
                                                            <TableCell key={cell.id}>
                                                                {flexRender(cell.column.columnDef.cell, cell.getContext())}
                                                            </TableCell>
                                                        ))}
                                                    </TableRow>
                                                ))
                                            ) : (
                                                <TableRow>
                                                    <TableCell colSpan={membershipColumns.length} className="h-24 text-center">
                                                        No results.
                                                    </TableCell>
                                                </TableRow>
                                            )}
                                        </TableBody>
                                    </Table>
                                </div>
                                <div className="flex items-center justify-end space-x-2 py-4">
                                    <div className="space-x-2">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() => membershipTable.previousPage()}
                                            disabled={!membershipTable.getCanPreviousPage()}
                                            className="small-font-size"
                                        >
                                            Previous
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() => membershipTable.nextPage()}
                                            disabled={!membershipTable.getCanNextPage()}
                                            className="small-font-size"
                                        >
                                            Next
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <ToastContainer />
        </AppLayout>
    );
}
