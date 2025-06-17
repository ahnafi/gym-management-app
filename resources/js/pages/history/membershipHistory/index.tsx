"use client"

import { DatePicker } from "@/components/DatePicker"
import { Button } from "@/components/ui/button"
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog"
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { Input } from "@/components/ui/input"
import SearchableSelect from "@/components/ui/SearchableSelect"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import AppLayout from "@/layouts/app-layout"
import type { BreadcrumbItem, MembershipHistoryFull, SimpleOption } from "@/types"
import { Head } from "@inertiajs/react"
import {
    type ColumnFiltersState,
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    type SortingState,
    useReactTable,
    type VisibilityState,
} from "@tanstack/react-table"
import { ChevronDown, Boxes, X, Search, Filter, CreditCard, Users, Clock, CheckCircle, AlertCircle } from "lucide-react"
import type * as React from "react"
import { useEffect, useState } from "react"
import { toast, ToastContainer } from "react-toastify"
import { membershipColumnLabels, getMembershipColumns} from "./tableConfig"

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Riwayat Membership",
        href: "/membership-histories",
    },
]

export default function MembershipHistories({
                                                membershipHistories,
                                                membershipPackages,
                                            }: {
    membershipHistories: MembershipHistoryFull[]
    membershipPackages: SimpleOption[]
}) {
    const membershipColumns = getMembershipColumns();

    // Membership Table State
    const [membershipSorting, setMembershipSorting] = useState<SortingState>([])
    const [membershipFilters, setMembershipFilters] = useState<ColumnFiltersState>([])
    const [membershipVisibility, setMembershipVisibility] = useState<VisibilityState>({})
    const [membershipSelection, setMembershipSelection] = useState({})
    const [membershipRows, setMembershipRows] = useState<number>(10)

    // Membership Table Filter State
    const [membershipSelectedPackage, setMembershipSelectedPackage] = useState<SimpleOption | null>(null)
    const [membershipInitialDate, setMembershipInitialDate] = useState<Date | undefined>()
    const [membershipFinalDate, setMembershipFinalDate] = useState<Date | undefined>()
    const [membershipFinalDateKey, setMembershipFinalDateKey] = useState<number>(Date.now())

    // Alert State
    const [alertMessage, setAlertMessage] = useState<string | null>(null)
    const [isFilterDialogOpen, setIsFilterDialogOpen] = useState(false)

    // Calculate stats
    const totalMemberships = membershipHistories.length
    const activeMemberships = membershipHistories.filter((m) => m.status === "active").length
    const expiredMemberships = membershipHistories.filter((m) => m.status === "expired").length

    // Initial Date Select Handlers
    const handleInitialDateSelect = (
        date: Date | undefined,
        setInitialDate: (date: Date | undefined) => void,
        setFinalDate: (date: Date | undefined) => void,
        finalDate: Date | undefined,
    ) => {
        const selected = date ?? new Date()
        setInitialDate(selected)

        if (!finalDate || (date && finalDate.getTime() === selected.getTime())) {
            setFinalDate(selected)
        } else if (selected.getTime() > finalDate.getTime()) {
            setAlertMessage("Tanggal awal tidak boleh lebih besar dari tanggal akhir")
            setFinalDate(selected)
        } else {
            setAlertMessage(null)
        }
    }

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
            setFinalDate(date)
            return
        }

        if (!initialDate) {
            setInitialDate(date)
            setFinalDate(date)
            setAlertMessage(null)
            return
        }

        if (date.getTime() === initialDate.getTime()) {
            setFinalDate(date)
        } else if (date.getTime() < initialDate.getTime()) {
            setAlertMessage("Tanggal akhir tidak boleh lebih kecil dari tanggal awal")
            setFinalDate(initialDate)
            setFinalDateKey(Date.now())
        } else {
            setFinalDate(date)
            setAlertMessage(null)
        }
    }

    const membershipTable = useReactTable<MembershipHistoryFull>({
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
    })

    // Column Filter Update
    const updateColumnFilter = (
        setFilters: React.Dispatch<React.SetStateAction<ColumnFiltersState>>,
        columnId: string,
        value: unknown,
    ) => {
        setFilters((prevFilters) => {
            const otherFilters = prevFilters.filter((f) => f.id !== columnId)
            if (value === undefined || value === null || value === "") {
                return otherFilters
            }
            return [...otherFilters, { id: columnId, value }]
        })
    }

    // Effects for filters
    useEffect(() => {
        if (membershipInitialDate) {
            updateColumnFilter(setMembershipFilters, "start_date", {
                start: membershipInitialDate,
                end: membershipFinalDate ?? membershipInitialDate,
            })
        } else {
            updateColumnFilter(setMembershipFilters, "start_date", undefined)
        }
    }, [membershipInitialDate, membershipFinalDate])

    useEffect(() => {
        if (membershipSelectedPackage?.name) {
            updateColumnFilter(setMembershipFilters, "membership_package_name", membershipSelectedPackage.name)
        } else {
            updateColumnFilter(setMembershipFilters, "membership_package_name", undefined)
        }
    }, [membershipSelectedPackage])

    useEffect(() => {
        membershipTable.setPageSize(membershipRows)
    }, [membershipRows, membershipTable])

    useEffect(() => {
        if (alertMessage) {
            toast.error(alertMessage, {
                position: "top-center",
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined,
            })
            setAlertMessage(null)
        }
    }, [alertMessage])

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Riwayat Membership" />

            {/* Hero Section */}
            <div className="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20 py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <div className="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 mb-4">
                            <CreditCard className="size-4 mr-2" />
                            Riwayat Membership
                        </div>
                        <h1 className="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl">
                            <span className="block">Riwayat</span>
                            <span className="block text-red-600 dark:text-red-400">Membership Anda</span>
                        </h1>
                        <p className="mt-3 max-w-md mx-auto text-base text-gray-500 dark:text-gray-400 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                            Pantau dan kelola semua aktivitas membership Anda dalam satu tempat
                        </p>
                    </div>
                </div>
            </div>

            {/* Stats Section */}
            <div className="bg-white dark:bg-zinc-900 py-8 border-b border-gray-200 dark:border-gray-800">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div className="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20 rounded-xl p-6 text-center">
                            <div className="flex items-center justify-center w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full mx-auto mb-3">
                                <Users className="size-6 text-red-600 dark:text-red-400" />
                            </div>
                            <div className="text-2xl font-bold text-red-600 dark:text-red-400">{totalMemberships}</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Total Membership</div>
                        </div>
                        <div className="bg-gradient-to-r from-orange-50 to-yellow-50 dark:from-orange-950/20 dark:to-yellow-950/20 rounded-xl p-6 text-center">
                            <div className="flex items-center justify-center w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-full mx-auto mb-3">
                                <CheckCircle className="size-6 text-orange-600 dark:text-orange-400" />
                            </div>
                            <div className="text-2xl font-bold text-orange-600 dark:text-orange-400">{activeMemberships}</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Membership Aktif</div>
                        </div>
                        <div className="bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-950/20 dark:to-amber-950/20 rounded-xl p-6 text-center">
                            <div className="flex items-center justify-center w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-full mx-auto mb-3">
                                <Clock className="size-6 text-yellow-600 dark:text-yellow-400" />
                            </div>
                            <div className="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{expiredMemberships}</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Membership Berakhir</div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <div className=" px-4 sm:px-6 lg:px-8 py-8">
                <div className="bg-white dark:bg-zinc-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    {/* Filters Section */}
                    <div className="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div className="flex items-center justify-between mb-4">
                            <h2 className="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <Filter className="size-5 text-red-600" />
                                Filter & Pencarian
                            </h2>
                        </div>

                        {/* Desktop Filters */}
                        <div className="hidden lg:flex flex-wrap gap-4 items-end">
                            <div className="flex-1 min-w-[200px]">
                                <SearchableSelect
                                    label="Jenis Membership"
                                    options={membershipPackages}
                                    selectedOption={membershipSelectedPackage}
                                    setSelectedOption={setMembershipSelectedPackage}
                                    placeholder="Filter Jenis Membership..."
                                    searchIcon={<Boxes size={16} />}
                                />
                            </div>
                            <div className="flex-1 min-w-[300px]">
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Rentang Tanggal
                                </label>
                                <div className="flex gap-2 items-center">
                                    <DatePicker
                                        value={membershipInitialDate}
                                        placeholder="Tanggal Awal"
                                        onDateSelect={(date) =>
                                            handleInitialDateSelect(
                                                date,
                                                setMembershipInitialDate,
                                                setMembershipFinalDate,
                                                membershipFinalDate,
                                            )
                                        }
                                    />
                                    <span className="text-gray-500">-</span>
                                    <DatePicker
                                        key={membershipFinalDateKey}
                                        value={membershipFinalDate}
                                        placeholder="Tanggal Akhir"
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
                                    {(membershipInitialDate || membershipFinalDate) && (
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => {
                                                setMembershipInitialDate(undefined)
                                                setMembershipFinalDate(undefined)
                                                setMembershipFilters((prev) => prev.filter((f) => f.id !== "start_date"))
                                            }}
                                            className="text-red-600 hover:text-red-700 hover:bg-red-50"
                                        >
                                            <X className="size-4" />
                                        </Button>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Mobile Filter Button */}
                        <div className="lg:hidden">
                            <Dialog open={isFilterDialogOpen} onOpenChange={setIsFilterDialogOpen}>
                                <DialogTrigger asChild>
                                    <Button className="w-full bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white">
                                        <Filter className="size-4 mr-2" />
                                        Filter Data
                                    </Button>
                                </DialogTrigger>
                                <DialogContent className="max-w-md">
                                    <DialogHeader>
                                        <DialogTitle className="flex items-center gap-2">
                                            <Filter className="size-5 text-red-600" />
                                            Filter Data
                                        </DialogTitle>
                                    </DialogHeader>
                                    <div className="space-y-4">
                                        <SearchableSelect
                                            label="Jenis Membership"
                                            options={membershipPackages}
                                            selectedOption={membershipSelectedPackage}
                                            setSelectedOption={setMembershipSelectedPackage}
                                            placeholder="Filter Jenis Membership..."
                                            searchIcon={<Boxes size={16} />}
                                        />
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Rentang Tanggal
                                            </label>
                                            <div className="space-y-2">
                                                <DatePicker
                                                    value={membershipInitialDate}
                                                    placeholder="Tanggal Awal"
                                                    onDateSelect={(date) =>
                                                        handleInitialDateSelect(
                                                            date,
                                                            setMembershipInitialDate,
                                                            setMembershipFinalDate,
                                                            membershipFinalDate,
                                                        )
                                                    }
                                                />
                                                <DatePicker
                                                    key={membershipFinalDateKey}
                                                    value={membershipFinalDate}
                                                    placeholder="Tanggal Akhir"
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
                                                {(membershipInitialDate || membershipFinalDate) && (
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => {
                                                            setMembershipInitialDate(undefined)
                                                            setMembershipFinalDate(undefined)
                                                            setMembershipFilters((prev) => prev.filter((f) => f.id !== "start_date"))
                                                        }}
                                                        className="w-full text-red-600 border-red-200 hover:bg-red-50"
                                                    >
                                                        <X className="size-4 mr-2" />
                                                        Hapus Filter Tanggal
                                                    </Button>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </DialogContent>
                            </Dialog>
                        </div>
                    </div>

                    {/* Table Controls */}
                    <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div className="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                            <div className="flex-1 max-w-sm">
                                <div className="relative">
                                    <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 size-4" />
                                    <Input
                                        placeholder="Cari kode membership..."
                                        value={(membershipTable.getColumn("code")?.getFilterValue() as string) ?? ""}
                                        onChange={(e) => membershipTable.getColumn("code")?.setFilterValue(e.target.value)}
                                        className="pl-10 border-gray-300 focus:border-red-500 focus:ring-red-500"
                                    />
                                </div>
                            </div>
                            <div className="flex gap-2">
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button variant="outline" className="border-gray-300 hover:border-red-300">
                                            Kolom <ChevronDown className="ml-2 size-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end" className="w-48">
                                        {membershipTable
                                            .getAllColumns()
                                            .filter((column) => column.getCanHide())
                                            .map((column) => (
                                                <DropdownMenuCheckboxItem
                                                    key={column.id}
                                                    checked={column.getIsVisible()}
                                                    onCheckedChange={(value) => column.toggleVisibility(!!value)}
                                                >
                                                    {membershipColumnLabels[column.id] ?? column.id}
                                                </DropdownMenuCheckboxItem>
                                            ))}
                                    </DropdownMenuContent>
                                </DropdownMenu>
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button variant="outline" className="border-gray-300 hover:border-red-300">
                                            {membershipRows} Baris <ChevronDown className="ml-2 size-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        {[10, 25, 50, 100].map((size) => (
                                            <DropdownMenuCheckboxItem
                                                key={size}
                                                checked={membershipRows === size}
                                                onCheckedChange={() => setMembershipRows(size)}
                                            >
                                                {size} baris
                                            </DropdownMenuCheckboxItem>
                                        ))}
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>
                        </div>
                    </div>

                    {/* Table */}
                    <div className="overflow-x-auto">
                        <Table>
                            <TableHeader className="bg-gray-50 dark:bg-gray-800">
                                {membershipTable.getHeaderGroups().map((headerGroup) => (
                                    <TableRow key={headerGroup.id}>
                                        {headerGroup.headers.map((header) => (
                                            <TableHead key={header.id} className="font-semibold text-gray-900 dark:text-white">
                                                {header.isPlaceholder ? null : flexRender(header.column.columnDef.header, header.getContext())}
                                            </TableHead>
                                        ))}
                                    </TableRow>
                                ))}
                            </TableHeader>
                            <TableBody>
                                {membershipTable.getRowModel().rows?.length ? (
                                    membershipTable.getRowModel().rows.map((row) => (
                                        <TableRow key={row.id} className="hover:bg-red-50 dark:hover:bg-red-950/10 transition-colors">
                                            {row.getVisibleCells().map((cell) => (
                                                <TableCell key={cell.id}>{flexRender(cell.column.columnDef.cell, cell.getContext())}</TableCell>
                                            ))}
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={membershipColumns.length} className="h-32 text-center">
                                            <div className="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                                <AlertCircle className="size-12 mb-4 text-gray-300" />
                                                <p className="text-lg font-medium">Tidak ada data ditemukan</p>
                                                <p className="text-sm">Coba ubah filter atau kriteria pencarian Anda</p>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </div>

                    {/* Pagination */}
                    <div className="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        <div className="flex items-center justify-between">
                            <div className="text-sm text-gray-700 dark:text-gray-300">
                                Menampilkan {membershipTable.getRowModel().rows.length} dari {membershipHistories.length} data
                            </div>
                            <div className="flex items-center space-x-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => membershipTable.previousPage()}
                                    disabled={!membershipTable.getCanPreviousPage()}
                                    className="border-gray-300 hover:border-red-300 hover:text-red-600"
                                >
                                    Sebelumnya
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => membershipTable.nextPage()}
                                    disabled={!membershipTable.getCanNextPage()}
                                    className="border-gray-300 hover:border-red-300 hover:text-red-600"
                                >
                                    Selanjutnya
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <ToastContainer />
        </AppLayout>
    )
}
