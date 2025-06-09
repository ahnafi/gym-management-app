('use-client');

import { DatePicker } from '@/components/DatePicker';
import { Button } from '@/components/ui/button';
import DropdownSelect from '@/components/ui/DropdownSelect';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import SearchableSelect from '@/components/ui/SearchableSelect';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { usePage } from '@inertiajs/react';
import {
    type BreadcrumbItem,
    PaymentHistory,
    AlertMessage,
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
import { ChevronDown, Boxes, X, Check } from 'lucide-react';
import * as React from 'react';
import { useEffect, useState } from 'react';
import { toast, ToastContainer } from 'react-toastify';
import {
    paymentColumnLabels,
    getPaymentColumns,
    // paymentStatusOptions,
    purchasableTypeOptions
} from './tableConfig';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pembayaran',
        href: '/payments',
    },
];

export default function Payments({
                                                payments,
                                                purchasables
                                            }: {
    payments: PaymentHistory[];
    purchasables: SimpleOption[];
}) {
    const { props } = usePage<{ flash?: { alert?: AlertMessage } }>();
    console.log('Inertia props:', props.flash);

    const [paymentSorting, setPaymentSorting] = useState<SortingState>([]);
    const [paymentFilters, setPaymentFilters] = useState<ColumnFiltersState>([]);
    const [paymentVisibility, setPaymentVisibility] = useState<VisibilityState>({});
    const [paymentSelection, setPaymentSelection] = useState({});
    const [paymentRows, setPaymentRows] = useState<number>(10);

    // Submission Table Filter State
    const [paymentSelectedType, setPaymentSelectedType] = useState<SimpleOption | null>(null);
    const [paymentSelectedPurchasable, setPaymentPurchasable] = useState<SimpleOption | null>(null);

    const [paymentInitialDate, setPaymentInitialDate] = useState<Date | undefined>();
    const [paymentFinalDate, setPaymentFinalDate] = useState<Date | undefined>();
    const [paymentFinalDateKey, setPaymentFinalDateKey] = useState<number>(Date.now());

    // Alert State
    const [alertMessage, setAlertMessage] = useState<AlertMessage | null>(null);

    const paymentColumns = getPaymentColumns(setAlertMessage);

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
            setAlertMessage({
                message: 'Tanggal awal tidak boleh lebih besar daripada tanggal akhir',
                type: 'error',
            });
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
        setAlertMessage: (msg: AlertMessage | null) => void,
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
            setAlertMessage({
                message: 'Tanggal akhir tidak boleh lebih kecil daripada tanggal awal',
                type: 'error',
            });
            setFinalDate(initialDate);
            setFinalDateKey(Date.now());
        } else {
            setFinalDate(date);
            setAlertMessage(null);
        }
    };

    const paymentTable = useReactTable<PaymentHistory>({
        data: payments,
        columns: paymentColumns,
        onSortingChange: setPaymentSorting,
        onColumnFiltersChange: setPaymentFilters,
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        onColumnVisibilityChange: setPaymentVisibility,
        onRowSelectionChange: setPaymentSelection,
        state: {
            sorting: paymentSorting,
            columnFilters: paymentFilters,
            columnVisibility: paymentVisibility,
            rowSelection: paymentSelection,
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
        if (paymentInitialDate) {
            updateColumnFilter(setPaymentFilters, 'created_at', {
                start: paymentInitialDate,
                end: paymentFinalDate ?? paymentInitialDate,
            });
        } else {
            updateColumnFilter(setPaymentFilters, 'created_at', undefined);
        }
    }, [paymentInitialDate, paymentFinalDate]);

    const useColumnFilterEffect = (
        selectedOption: SimpleOption | null,
        setFilters: React.Dispatch<React.SetStateAction<ColumnFiltersState>>,
        columnId: string,
    ) => {
        useEffect(() => {
            if (selectedOption?.name) {
                updateColumnFilter(setFilters, columnId, selectedOption.name);
            } else {
                updateColumnFilter(setFilters, columnId, undefined);
            }
        }, [selectedOption, columnId, setFilters]);
    };

    useEffect(() => {
        if (paymentSelectedPurchasable?.name) {
            updateColumnFilter(setPaymentFilters, 'payment_package_name', paymentSelectedPurchasable.name);
        } else {
            updateColumnFilter(setPaymentFilters, 'payment_package_name', undefined);
        }
    }, [paymentSelectedPurchasable]);

    // Row Pagination Effect
    const usePageSizeEffect = <T,>(table: TanStackTable<T>, rows: number) => {
        useEffect(() => {
            table.setPageSize(rows);
        }, [rows, table]);
    };

    usePageSizeEffect(paymentTable, paymentRows);

    // Alert Message
    useEffect(() => {
        const messageToShow = alertMessage ?? props.flash?.alert ?? null;

        if (messageToShow) {
            toast(messageToShow.message, {
                type: messageToShow.type,
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
            });

            setAlertMessage(null);
        }
    }, [alertMessage, props.flash?.alert]);

    // Filter Dialog State
    const [isFilterDialogOpen, setIsFilterDialogOpen] = useState(false);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Riwayat Membership" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-hidden rounded-xl p-4">
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div className="payment col-span-full space-y-2">
                        <h1 className="title font-semibold">Riwayat Membership</h1>
                        <div className="payment-table-filters small-font-size mb-2 flex hidden justify-end gap-4 lg:mb-4 lg:flex lg:flex-wrap">
                            {/* Daftar filter untuk layar besar */}
                            <div className="status-type">
                                <DropdownSelect
                                    label="Filter Jenis Pembelian"
                                    options={purchasableTypeOptions}
                                    selectedOption={paymentSelectedType}
                                    setSelectedOption={setPaymentSelectedType}
                                    placeholder="Filter Jenis Pembelian..."
                                    icon={<Check size={18} />}
                                />
                            </div>
                            <div className="test-type">
                                <SearchableSelect
                                    label="Jenis Produk"
                                    options={purchasables}
                                    selectedOption={paymentSelectedPurchasable}
                                    setSelectedOption={setPaymentPurchasable}
                                    placeholder="Filter Nama Produk..."
                                    searchIcon={<Boxes size={16} />}
                                />
                            </div>
                            <div className="date-range-picker">
                                <label className="text-foreground font-medium">Tanggal</label>
                                <div className="flex gap-3">
                                    <div className="initial-date">
                                        <DatePicker
                                            value={paymentInitialDate}
                                            placeholder="Pilih Tanggal Awal"
                                            onDateSelect={(date) =>
                                                handleInitialDateSelect(date, setPaymentInitialDate, setPaymentFinalDate, paymentFinalDate)
                                            }
                                        />
                                    </div>
                                    <div className="flex items-center justify-center">-</div>
                                    <div className="final-date">
                                        <DatePicker
                                            key={paymentFinalDateKey}
                                            value={paymentInitialDate}
                                            placeholder="Pilih Tanggal Akhir"
                                            onDateSelect={(date) =>
                                                handleFinalDateSelect(
                                                    date,
                                                    paymentInitialDate,
                                                    setPaymentInitialDate,
                                                    setPaymentFinalDate,
                                                    setAlertMessage,
                                                    setPaymentFinalDateKey,
                                                )
                                            }
                                        />
                                    </div>
                                </div>


                                {(paymentInitialDate || paymentFinalDate) && (
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setPaymentInitialDate(undefined);
                                            setPaymentFinalDate(undefined);
                                            setPaymentFilters((prev) => prev.filter((f) => f.id !== 'created_at'));
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
                                                        value={paymentInitialDate}
                                                        placeholder="Pilih Tanggal Awal"
                                                        onDateSelect={(date) =>
                                                            handleInitialDateSelect(
                                                                date,
                                                                setPaymentInitialDate,
                                                                setPaymentFinalDate,
                                                                paymentFinalDate,
                                                            )
                                                        }
                                                    />
                                                </div>
                                                <div className="final-date flex flex-col">
                                                    <DatePicker
                                                        key={paymentFinalDateKey}
                                                        value={paymentInitialDate}
                                                        placeholder="Pilih Tanggal Akhir"
                                                        onDateSelect={(date) =>
                                                            handleFinalDateSelect(
                                                                date,
                                                                paymentInitialDate,
                                                                setPaymentInitialDate,
                                                                setPaymentFinalDate,
                                                                setAlertMessage,
                                                                setPaymentFinalDateKey,
                                                            )
                                                        }
                                                    />
                                                </div>
                                            </div>
                                            {(paymentInitialDate || paymentFinalDate) && (
                                                <button
                                                    type="button"
                                                    onClick={() => {
                                                        setPaymentInitialDate(undefined);
                                                        setPaymentFinalDate(undefined);
                                                        setPaymentFilters((prev) => prev.filter((f) => f.id !== 'created_at'));
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
                        <div className="payment-table-main">
                            <div className="payment-table-option mb-2 flex justify-between lg:mb-4">
                                <div className="flex w-full justify-end gap-2 flex-wrap">
                                    <div className="code-search flex flex-col">
                                        <Input
                                            placeholder="Cari Kode Pembayaran..."
                                            value={(paymentTable.getColumn('code')?.getFilterValue() as string) ?? ''}
                                            onChange={(e) => paymentTable.getColumn('code')?.setFilterValue(e.target.value)}
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
                                                {paymentTable
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
                                                                {paymentColumnLabels[column.id] ?? column.id}
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
                                                    Tampilkan {paymentRows} Baris <ChevronDown className="ml-1 h-4 w-4" />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                {[10, 25, 50, 100].map((size) => (
                                                    <DropdownMenuCheckboxItem
                                                        key={size}
                                                        checked={paymentRows === size}
                                                        onCheckedChange={() => setPaymentRows(size)}
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
                            <div className="payment-table-body">
                                <div className="rounded-md border">
                                    <Table className="small-font-size">
                                        <TableHeader>
                                            {paymentTable.getHeaderGroups().map((headerGroup) => (
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
                                            {paymentTable.getRowModel().rows?.length ? (
                                                paymentTable.getRowModel().rows.map((row) => (
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
                                                    <TableCell colSpan={paymentColumns.length} className="h-24 text-center">
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
                                            onClick={() => paymentTable.previousPage()}
                                            disabled={!paymentTable.getCanPreviousPage()}
                                            className="small-font-size"
                                        >
                                            Previous
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() => paymentTable.nextPage()}
                                            disabled={!paymentTable.getCanNextPage()}
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
