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
import type { BreadcrumbItem, GymClassHistory, SimpleOption } from "@/types"
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
import { ChevronDown, X, Dumbbell, Search, Filter, Clock, CheckCircle, AlertCircle, Trophy } from "lucide-react"
import type * as React from "react"
import { useEffect, useState } from "react"
import { toast, ToastContainer } from "react-toastify"
import { gymClassColumnLabels, getGymClassColumns } from "./tableConfig"

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Riwayat Kelas Gym",
        href: "/gym-class-history",
    },
]

export default function GymClassHistories({
                                              gymClassHistories,
                                              gymClasses,
                                          }: {
    gymClassHistories: GymClassHistory[]
    gymClasses: SimpleOption[]
}) {

    const gymClassColumns = getGymClassColumns()

    // Gym Class Table State
    const [gymClassSorting, setGymClassSorting] = useState<SortingState>([])
    const [gymClassFilters, setGymClassFilters] = useState<ColumnFiltersState>([])
    const [gymClassVisibility, setGymClassVisibility] = useState<VisibilityState>({})
    const [gymClassSelection, setGymClassSelection] = useState({})
    const [gymClassRows, setGymClassRows] = useState<number>(10)

    // Gym Class Table Filter State
    const [gymClassSelectedClass, setGymClassSelectedClass] = useState<SimpleOption | null>(null)
    const [gymClassInitialDate, setGymClassInitialDate] = useState<Date | undefined>()
    const [gymClassFinalDate, setGymClassFinalDate] = useState<Date | undefined>()
    const [gymClassFinalDateKey, setGymClassFinalDateKey] = useState<number>(Date.now())

    // Alert State
    const [alertMessage, setAlertMessage] = useState<string | null>(null)
    const [isFilterDialogOpen, setIsFilterDialogOpen] = useState(false)

    // Calculate stats
    const totalClasses = gymClassHistories.length
    const completedClasses = gymClassHistories.filter((c) => c.status === "attended").length
    const upcomingClasses = gymClassHistories.filter((c) => c.status === "assigned").length

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

    const gymClassTable = useReactTable<GymClassHistory>({
        data: gymClassHistories,
        columns: gymClassColumns,
        onSortingChange: setGymClassSorting,
        onColumnFiltersChange: setGymClassFilters,
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        onColumnVisibilityChange: setGymClassVisibility,
        onRowSelectionChange: setGymClassSelection,
        state: {
            sorting: gymClassSorting,
            columnFilters: gymClassFilters,
            columnVisibility: gymClassVisibility,
            rowSelection: gymClassSelection,
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
        if (gymClassInitialDate) {
            updateColumnFilter(setGymClassFilters, "date", {
                start: gymClassInitialDate,
                end: gymClassFinalDate ?? gymClassInitialDate,
            })
        } else {
            updateColumnFilter(setGymClassFilters, "date", undefined)
        }
    }, [gymClassInitialDate, gymClassFinalDate])

    useEffect(() => {
        if (gymClassSelectedClass?.name) {
            updateColumnFilter(setGymClassFilters, "class_name", gymClassSelectedClass.name)
        } else {
            updateColumnFilter(setGymClassFilters, "class_name", undefined)
        }
    }, [gymClassSelectedClass])

    useEffect(() => {
        gymClassTable.setPageSize(gymClassRows)
    }, [gymClassRows, gymClassTable])

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
            <Head title="Riwayat Kelas Gym" />

            {/* Hero Section */}
            <div className="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20 py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <div className="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 mb-4">
                            <Dumbbell className="size-4 mr-2" />
                            Riwayat Kelas Gym
                        </div>
                        <h1 className="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl">
                            <span className="block">Riwayat</span>
                            <span className="block text-red-600 dark:text-red-400">Kelas Gym Anda</span>
                        </h1>
                        <p className="mt-3 max-w-md mx-auto text-base text-gray-500 dark:text-gray-400 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                            Pantau dan kelola semua aktivitas kelas gym Anda dalam satu tempat
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
                                <Dumbbell className="size-6 text-red-600 dark:text-red-400" />
                            </div>
                            <div className="text-2xl font-bold text-red-600 dark:text-red-400">{totalClasses}</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Total Kelas</div>
                        </div>
                        <div className="bg-gradient-to-r from-orange-50 to-yellow-50 dark:from-orange-950/20 dark:to-yellow-950/20 rounded-xl p-6 text-center">
                            <div className="flex items-center justify-center w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-full mx-auto mb-3">
                                <CheckCircle className="size-6 text-orange-600 dark:text-orange-400" />
                            </div>
                            <div className="text-2xl font-bold text-orange-600 dark:text-orange-400">{completedClasses}</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Kelas Selesai</div>
                        </div>
                        <div className="bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-950/20 dark:to-amber-950/20 rounded-xl p-6 text-center">
                            <div className="flex items-center justify-center w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-full mx-auto mb-3">
                                <Clock className="size-6 text-yellow-600 dark:text-yellow-400" />
                            </div>
                            <div className="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{upcomingClasses}</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Kelas Mendatang</div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <div className="max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
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
                                    label="Jenis Kelas"
                                    options={gymClasses}
                                    selectedOption={gymClassSelectedClass}
                                    setSelectedOption={setGymClassSelectedClass}
                                    placeholder="Filter Jenis Kelas..."
                                    searchIcon={<Dumbbell size={16} />}
                                />
                            </div>
                            <div className="flex-1 min-w-[300px]">
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Rentang Tanggal
                                </label>
                                <div className="flex gap-2 items-center">
                                    <DatePicker
                                        value={gymClassInitialDate}
                                        placeholder="Tanggal Awal"
                                        onDateSelect={(date) =>
                                            handleInitialDateSelect(date, setGymClassInitialDate, setGymClassFinalDate, gymClassFinalDate)
                                        }
                                    />
                                    <span className="text-gray-500">-</span>
                                    <DatePicker
                                        key={gymClassFinalDateKey}
                                        value={gymClassFinalDate}
                                        placeholder="Tanggal Akhir"
                                        onDateSelect={(date) =>
                                            handleFinalDateSelect(
                                                date,
                                                gymClassInitialDate,
                                                setGymClassInitialDate,
                                                setGymClassFinalDate,
                                                setAlertMessage,
                                                setGymClassFinalDateKey,
                                            )
                                        }
                                    />
                                    {(gymClassInitialDate || gymClassFinalDate) && (
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => {
                                                setGymClassInitialDate(undefined)
                                                setGymClassFinalDate(undefined)
                                                setGymClassFilters((prev) => prev.filter((f) => f.id !== "date"))
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
                                            label="Jenis Kelas"
                                            options={gymClasses}
                                            selectedOption={gymClassSelectedClass}
                                            setSelectedOption={setGymClassSelectedClass}
                                            placeholder="Filter Jenis Kelas..."
                                            searchIcon={<Dumbbell size={16} />}
                                        />
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Rentang Tanggal
                                            </label>
                                            <div className="space-y-2">
                                                <DatePicker
                                                    value={gymClassInitialDate}
                                                    placeholder="Tanggal Awal"
                                                    onDateSelect={(date) =>
                                                        handleInitialDateSelect(
                                                            date,
                                                            setGymClassInitialDate,
                                                            setGymClassFinalDate,
                                                            gymClassFinalDate,
                                                        )
                                                    }
                                                />
                                                <DatePicker
                                                    key={gymClassFinalDateKey}
                                                    value={gymClassFinalDate}
                                                    placeholder="Tanggal Akhir"
                                                    onDateSelect={(date) =>
                                                        handleFinalDateSelect(
                                                            date,
                                                            gymClassInitialDate,
                                                            setGymClassInitialDate,
                                                            setGymClassFinalDate,
                                                            setAlertMessage,
                                                            setGymClassFinalDateKey,
                                                        )
                                                    }
                                                />
                                                {(gymClassInitialDate || gymClassFinalDate) && (
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => {
                                                            setGymClassInitialDate(undefined)
                                                            setGymClassFinalDate(undefined)
                                                            setGymClassFilters((prev) => prev.filter((f) => f.id !== "date"))
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
                                        placeholder="Cari nama kelas..."
                                        value={(gymClassTable.getColumn("class_name")?.getFilterValue() as string) ?? ""}
                                        onChange={(e) => gymClassTable.getColumn("class_name")?.setFilterValue(e.target.value)}
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
                                        {gymClassTable
                                            .getAllColumns()
                                            .filter((column) => column.getCanHide())
                                            .map((column) => (
                                                <DropdownMenuCheckboxItem
                                                    key={column.id}
                                                    checked={column.getIsVisible()}
                                                    onCheckedChange={(value) => column.toggleVisibility(!!value)}
                                                >
                                                    {gymClassColumnLabels[column.id] ?? column.id}
                                                </DropdownMenuCheckboxItem>
                                            ))}
                                    </DropdownMenuContent>
                                </DropdownMenu>
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button variant="outline" className="border-gray-300 hover:border-red-300">
                                            {gymClassRows} Baris <ChevronDown className="ml-2 size-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        {[10, 25, 50, 100].map((size) => (
                                            <DropdownMenuCheckboxItem
                                                key={size}
                                                checked={gymClassRows === size}
                                                onCheckedChange={() => setGymClassRows(size)}
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
                                {gymClassTable.getHeaderGroups().map((headerGroup) => (
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
                                {gymClassTable.getRowModel().rows?.length ? (
                                    gymClassTable.getRowModel().rows.map((row) => (
                                        <TableRow key={row.id} className="hover:bg-red-50 dark:hover:bg-red-950/10 transition-colors">
                                            {row.getVisibleCells().map((cell) => (
                                                <TableCell key={cell.id}>{flexRender(cell.column.columnDef.cell, cell.getContext())}</TableCell>
                                            ))}
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={gymClassColumns.length} className="h-32 text-center">
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
                                Menampilkan {gymClassTable.getRowModel().rows.length} dari {gymClassHistories.length} data
                            </div>
                            <div className="flex items-center space-x-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => gymClassTable.previousPage()}
                                    disabled={!gymClassTable.getCanPreviousPage()}
                                    className="border-gray-300 hover:border-red-300 hover:text-red-600"
                                >
                                    Sebelumnya
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => gymClassTable.nextPage()}
                                    disabled={!gymClassTable.getCanNextPage()}
                                    className="border-gray-300 hover:border-red-300 hover:text-red-600"
                                >
                                    Selanjutnya
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Achievement Section */}
                <div className="mt-8 bg-gradient-to-r from-red-600 to-orange-600 rounded-2xl p-8 text-center text-white">
                    <div className="flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mx-auto mb-4">
                        <Trophy className="size-8" />
                    </div>
                    <h2 className="text-2xl font-bold mb-4">Terus Semangat!</h2>
                    <p className="text-red-100 mb-6 max-w-2xl mx-auto">
                        Setiap kelas yang Anda ikuti adalah langkah menuju versi terbaik dari diri Anda. Tetap konsisten dan raih
                        target fitness Anda!
                    </p>
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-md mx-auto">
                        <div className="bg-white/10 rounded-lg p-4">
                            <div className="text-2xl font-bold">{completedClasses}</div>
                            <div className="text-sm text-red-100">Kelas Selesai</div>
                        </div>
                        <div className="bg-white/10 rounded-lg p-4">
                            <div className="text-2xl font-bold">{Math.round((completedClasses / totalClasses) * 100) || 0}%</div>
                            <div className="text-sm text-red-100">Tingkat Kehadiran</div>
                        </div>
                        <div className="bg-white/10 rounded-lg p-4">
                            <div className="text-2xl font-bold">{upcomingClasses}</div>
                            <div className="text-sm text-red-100">Kelas Mendatang</div>
                        </div>
                    </div>
                </div>
            </div>

            <ToastContainer />
        </AppLayout>
    )
}
