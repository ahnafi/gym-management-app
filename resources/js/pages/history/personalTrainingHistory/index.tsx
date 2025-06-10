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
import type { BreadcrumbItem, PersonalTrainingHistory, SimpleOption } from "@/types"
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
import { ChevronDown, X, Search, Filter, User, CheckCircle, AlertCircle, Trophy, Target, Award } from "lucide-react"
import type * as React from "react"
import { useEffect, useState } from "react"
import { toast, ToastContainer } from "react-toastify"
import { personalTrainingColumnLabels, personalTrainingColumns } from "./tableConfig"

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Riwayat Personal Training",
        href: "/personal-trainer-history",
    },
]

export default function PersonalTrainerHistories({
                                                     personalTrainingHistories,
                                                     personalTrainers,
                                                     personalTrainerPackages,
                                                 }: {
    personalTrainingHistories: PersonalTrainingHistory[]
    personalTrainers: SimpleOption[]
    personalTrainerPackages: SimpleOption[]
}) {
    // Personal Training Table State
    const [PTSorting, setPTSorting] = useState<SortingState>([])
    const [PTFilters, setPTFilters] = useState<ColumnFiltersState>([])
    const [PTVisibility, setPTVisibility] = useState<VisibilityState>({})
    const [PTSelection, setPTSelection] = useState({})
    const [PTRows, setPTRows] = useState<number>(10)

    // Personal Training Table Filter State
    const [PTSelectedPackage, setPTSelectedPackage] = useState<SimpleOption | null>(null)
    const [PTInitialDate, setPTInitialDate] = useState<Date | undefined>()
    const [PTFinalDate, setPTFinalDate] = useState<Date | undefined>()
    const [PTFinalDateKey, setPTFinalDateKey] = useState<number>(Date.now())

    // Alert State
    const [alertMessage, setAlertMessage] = useState<string | null>(null)
    const [isFilterDialogOpen, setIsFilterDialogOpen] = useState(false)

    // Calculate stats
    const totalSessions = personalTrainingHistories.length
    const completedSessions = personalTrainingHistories.filter((s) => s.status === "completed").length
    const upcomingSessions = personalTrainingHistories.filter((s) => s.status === "active").length

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

    const PTTable = useReactTable<PersonalTrainingHistory>({
        data: personalTrainingHistories,
        columns: personalTrainingColumns,
        onSortingChange: setPTSorting,
        onColumnFiltersChange: setPTFilters,
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        onColumnVisibilityChange: setPTVisibility,
        onRowSelectionChange: setPTSelection,
        state: {
            sorting: PTSorting,
            columnFilters: PTFilters,
            columnVisibility: PTVisibility,
            rowSelection: PTSelection,
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
        if (PTInitialDate) {
            updateColumnFilter(setPTFilters, "start_date", {
                start: PTInitialDate,
                end: PTFinalDate ?? PTInitialDate,
            })
        } else {
            updateColumnFilter(setPTFilters, "start_date", undefined)
        }
    }, [PTInitialDate, PTFinalDate])

    useEffect(() => {
        if (PTSelectedPackage?.name) {
            updateColumnFilter(setPTFilters, "package_name", PTSelectedPackage.name)
        } else {
            updateColumnFilter(setPTFilters, "package_name", undefined)
        }
    }, [PTSelectedPackage])

    useEffect(() => {
        PTTable.setPageSize(PTRows)
    }, [PTRows, PTTable])

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
            <Head title="Riwayat Personal Training" />

            {/* Hero Section */}
            <div className="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20 py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <div className="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 mb-4">
                            <User className="size-4 mr-2" />
                            Riwayat Personal Training
                        </div>
                        <h1 className="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl">
                            <span className="block">Riwayat</span>
                            <span className="block text-red-600 dark:text-red-400">Personal Training Anda</span>
                        </h1>
                        <p className="mt-3 max-w-md mx-auto text-base text-gray-500 dark:text-gray-400 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                            Pantau dan kelola semua sesi personal training Anda dengan trainer profesional
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
                                <Target className="size-6 text-red-600 dark:text-red-400" />
                            </div>
                            <div className="text-2xl font-bold text-red-600 dark:text-red-400">{totalSessions}</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Total Sesi</div>
                        </div>
                        <div className="bg-gradient-to-r from-orange-50 to-yellow-50 dark:from-orange-950/20 dark:to-yellow-950/20 rounded-xl p-6 text-center">
                            <div className="flex items-center justify-center w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-full mx-auto mb-3">
                                <CheckCircle className="size-6 text-orange-600 dark:text-orange-400" />
                            </div>
                            <div className="text-2xl font-bold text-orange-600 dark:text-orange-400">{completedSessions}</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Sesi Selesai</div>
                        </div>
                        <div className="bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-950/20 dark:to-amber-950/20 rounded-xl p-6 text-center">
                            <div className="flex items-center justify-center w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-full mx-auto mb-3">
                                <Trophy className="size-6 text-yellow-600 dark:text-yellow-400" />
                            </div>
                            <div className="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{upcomingSessions}</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Sesi Mendatang</div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <div className="px-4 sm:px-6 lg:px-8 py-8">
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
                                    label="Paket Personal Trainer"
                                    options={personalTrainerPackages}
                                    selectedOption={PTSelectedPackage}
                                    setSelectedOption={setPTSelectedPackage}
                                    placeholder="Filter Paket Training..."
                                    searchIcon={<User size={16} />}
                                />
                            </div>
                            <div className="flex-1 min-w-[300px]">
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Rentang Tanggal
                                </label>
                                <div className="flex gap-2 items-center">
                                    <DatePicker
                                        value={PTInitialDate}
                                        placeholder="Tanggal Awal"
                                        onDateSelect={(date) =>
                                            handleInitialDateSelect(date, setPTInitialDate, setPTFinalDate, PTFinalDate)
                                        }
                                    />
                                    <span className="text-gray-500">-</span>
                                    <DatePicker
                                        key={PTFinalDateKey}
                                        value={PTFinalDate}
                                        placeholder="Tanggal Akhir"
                                        onDateSelect={(date) =>
                                            handleFinalDateSelect(
                                                date,
                                                PTInitialDate,
                                                setPTInitialDate,
                                                setPTFinalDate,
                                                setAlertMessage,
                                                setPTFinalDateKey,
                                            )
                                        }
                                    />
                                    {(PTInitialDate || PTFinalDate) && (
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => {
                                                setPTInitialDate(undefined)
                                                setPTFinalDate(undefined)
                                                setPTFilters((prev) => prev.filter((f) => f.id !== "start_date"))
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
                                            label="Paket Personal Trainer"
                                            options={personalTrainerPackages}
                                            selectedOption={PTSelectedPackage}
                                            setSelectedOption={setPTSelectedPackage}
                                            placeholder="Filter Paket Training..."
                                            searchIcon={<User size={16} />}
                                        />
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Rentang Tanggal
                                            </label>
                                            <div className="space-y-2">
                                                <DatePicker
                                                    value={PTInitialDate}
                                                    placeholder="Tanggal Awal"
                                                    onDateSelect={(date) =>
                                                        handleInitialDateSelect(date, setPTInitialDate, setPTFinalDate, PTFinalDate)
                                                    }
                                                />
                                                <DatePicker
                                                    key={PTFinalDateKey}
                                                    value={PTFinalDate}
                                                    placeholder="Tanggal Akhir"
                                                    onDateSelect={(date) =>
                                                        handleFinalDateSelect(
                                                            date,
                                                            PTInitialDate,
                                                            setPTInitialDate,
                                                            setPTFinalDate,
                                                            setAlertMessage,
                                                            setPTFinalDateKey,
                                                        )
                                                    }
                                                />
                                                {(PTInitialDate || PTFinalDate) && (
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => {
                                                            setPTInitialDate(undefined)
                                                            setPTFinalDate(undefined)
                                                            setPTFilters((prev) => prev.filter((f) => f.id !== "start_date"))
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
                                        placeholder="Cari nama trainer..."
                                        value={(PTTable.getColumn("trainer_nickname")?.getFilterValue() as string) ?? ""}
                                        onChange={(e) => PTTable.getColumn("trainer_nickname")?.setFilterValue(e.target.value)}
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
                                        {PTTable.getAllColumns()
                                            .filter((column) => column.getCanHide())
                                            .map((column) => (
                                                <DropdownMenuCheckboxItem
                                                    key={column.id}
                                                    checked={column.getIsVisible()}
                                                    onCheckedChange={(value) => column.toggleVisibility(!!value)}
                                                >
                                                    {personalTrainingColumnLabels[column.id] ?? column.id}
                                                </DropdownMenuCheckboxItem>
                                            ))}
                                    </DropdownMenuContent>
                                </DropdownMenu>
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button variant="outline" className="border-gray-300 hover:border-red-300">
                                            {PTRows} Baris <ChevronDown className="ml-2 size-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        {[10, 25, 50, 100].map((size) => (
                                            <DropdownMenuCheckboxItem
                                                key={size}
                                                checked={PTRows === size}
                                                onCheckedChange={() => setPTRows(size)}
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
                            <TableHeader className="bg-gray-50 dark:bg-zinc-800">
                                {PTTable.getHeaderGroups().map((headerGroup) => (
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
                                {PTTable.getRowModel().rows?.length ? (
                                    PTTable.getRowModel().rows.map((row) => (
                                        <TableRow key={row.id} className="hover:bg-red-50 dark:hover:bg-red-950/10 transition-colors">
                                            {row.getVisibleCells().map((cell) => (
                                                <TableCell key={cell.id}>{flexRender(cell.column.columnDef.cell, cell.getContext())}</TableCell>
                                            ))}
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={personalTrainingColumns.length} className="h-32 text-center">
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
                                Menampilkan {PTTable.getRowModel().rows.length} dari {personalTrainingHistories.length} data
                            </div>
                            <div className="flex items-center space-x-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => PTTable.previousPage()}
                                    disabled={!PTTable.getCanPreviousPage()}
                                    className="border-gray-300 hover:border-red-300 hover:text-red-600"
                                >
                                    Sebelumnya
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => PTTable.nextPage()}
                                    disabled={!PTTable.getCanNextPage()}
                                    className="border-gray-300 hover:border-red-300 hover:text-red-600"
                                >
                                    Selanjutnya
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Progress & Achievement Section */}
                <div className="mt-8 bg-gradient-to-r from-red-600 to-orange-600 rounded-2xl p-8 text-center text-white">
                    <div className="flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mx-auto mb-4">
                        <Award className="size-8" />
                    </div>
                    <h2 className="text-2xl font-bold mb-4">Perjalanan Fitness Anda</h2>
                    <p className="text-red-100 mb-6 max-w-2xl mx-auto">
                        Setiap sesi personal training adalah investasi untuk kesehatan dan kebugaran Anda. Terus konsisten dan raih
                        target yang telah ditetapkan!
                    </p>
                    <div className="grid grid-cols-1 sm:grid-cols-4 gap-4 max-w-2xl mx-auto">
                        <div className="bg-white/10 rounded-lg p-4">
                            <div className="text-2xl font-bold">{completedSessions}</div>
                            <div className="text-sm text-red-100">Sesi Selesai</div>
                        </div>
                        <div className="bg-white/10 rounded-lg p-4">
                            <div className="text-2xl font-bold">{Math.round((completedSessions / totalSessions) * 100) || 0}%</div>
                            <div className="text-sm text-red-100">Tingkat Kehadiran</div>
                        </div>
                        <div className="bg-white/10 rounded-lg p-4">
                            <div className="text-2xl font-bold">{upcomingSessions}</div>
                            <div className="text-sm text-red-100">Sesi Mendatang</div>
                        </div>
                        <div className="bg-white/10 rounded-lg p-4">
                            <div className="text-2xl font-bold">{personalTrainers.length}</div>
                            <div className="text-sm text-red-100">Trainer Tersedia</div>
                        </div>
                    </div>
                </div>
            </div>

            <ToastContainer />
        </AppLayout>
    )
}
