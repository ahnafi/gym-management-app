"use client"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import type { MembershipHistoryFull, SimpleOption } from "@/types"
import type { ColumnDef } from "@tanstack/react-table"
import { format, parseISO } from "date-fns"
import {
    Calendar,
    Info,
    Package,
    X,
    Clock,
    User,
    CreditCard,
    CheckCircle,
    AlertCircle,
    FileText,
    ArrowUpDown,
} from "lucide-react"

export const membershipColumnLabels: Record<string, string> = {
    code: "Kode Riwayat",
    start_date: "Tanggal Mulai",
    end_date: "Tanggal Akhir",
    membership_package_name: "Nama Paket",
    status: "Status",
    detail: "Detail",
}

export const membershipStatusOptions: SimpleOption[] = [
    { id: 1, name: "Active" },
    { id: 2, name: "Expired" },
]

export const getMembershipColumns = (
): ColumnDef<MembershipHistoryFull>[] => {
    const DetailCell = ({ row }: { row: { original: MembershipHistoryFull } }) => {
        const [openModal, setOpenModal] = useState(false)
        const membership = row.original

        return (
            <>
                <div className="flex justify-center w-[6rem]">
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => setOpenModal(true)}
                        className="border-orange-200 hover:bg-orange-50 hover:border-orange-300 dark:border-orange-800 dark:hover:bg-orange-950"
                    >
                        Detail
                    </Button>
                </div>

                {openModal && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center py-8 bg-black/50 dark:bg-black/70 backdrop-blur-sm">
                        <div className="relative bg-white dark:bg-zinc-900 p-6 rounded-2xl shadow-2xl max-w-md w-full text-zinc-800 dark:text-zinc-100 border border-orange-200 dark:border-orange-800 max-h-[90vh] overflow-y-auto">
                            <button
                                onClick={() => setOpenModal(false)}
                                className="absolute top-3 right-3 text-zinc-500 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-950"
                            >
                                <X className="w-5 h-5" />
                            </button>

                            <div className="mb-6">
                                <div className="flex items-center gap-3 mb-2">
                                    <div className="p-2 rounded-full bg-gradient-to-r from-orange-500 to-red-500 text-white">
                                        <Info className="w-5 h-5" />
                                    </div>
                                    <h2 className="text-xl font-semibold">Detail Membership</h2>
                                </div>
                                <div className="h-1 bg-gradient-to-r from-orange-500 to-red-500 rounded-full"></div>
                            </div>

                            <div className="text-sm space-y-4">
                                <div className="flex items-start gap-3 p-3 rounded-lg bg-orange-50 dark:bg-zinc-800">
                                    <Package className="w-4 h-4 mt-1 text-orange-600" />
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Kode Membership</span>
                                        <p className="text-zinc-900 dark:text-white font-mono">{membership.code || "Tidak ada kode"}</p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3 p-3 rounded-lg bg-red-50 dark:bg-zinc-800">
                                    {membership.status === "active" ? (
                                        <CheckCircle className="w-4 h-4 mt-1 text-green-600" />
                                    ) : (
                                        <AlertCircle className="w-4 h-4 mt-1 text-red-600" />
                                    )}
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Status Membership</span>
                                        <p className="text-zinc-900 dark:text-white">
                                            {membership.status === "active" ? "Aktif" : "Kadaluarsa"}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3 p-3 rounded-lg bg-yellow-50 dark:bg-zinc-800">
                                    <Calendar className="w-4 h-4 mt-1 text-yellow-600" />
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Periode Membership</span>
                                        <div className="space-y-1">
                                            <p className="text-zinc-900 dark:text-white">
                                                <span className="font-medium">Mulai:</span>{" "}
                                                {format(parseISO(membership.start_date), "dd MMMM yyyy")}
                                            </p>
                                            <p className="text-zinc-900 dark:text-white">
                                                <span className="font-medium">Berakhir:</span>{" "}
                                                {format(parseISO(membership.end_date), "dd MMMM yyyy")}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div className="p-3 rounded-lg bg-gradient-to-r from-orange-50 to-red-50 dark:from-zinc-800 dark:to-zinc-800 border-l-4 border-orange-500">
                                    <div className="flex items-center gap-2 mb-3">
                                        <Package className="w-4 h-4 text-orange-600" />
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Detail Paket Membership</span>
                                    </div>
                                    <div className="ml-6 space-y-2 text-sm">
                                        <div className="flex items-start gap-3 p-2 rounded bg-white dark:bg-zinc-700">
                                            <FileText className="w-3 h-3 mt-1 text-orange-600" />
                                            <div>
                                                <span className="font-medium text-zinc-600 dark:text-zinc-400">Nama Paket:</span>
                                                <p className="text-zinc-900 dark:text-white">{membership.membership_package.name}</p>
                                            </div>
                                        </div>

                                        {membership.membership_package.code && (
                                            <div className="flex items-start gap-3 p-2 rounded bg-white dark:bg-zinc-700">
                                                <Package className="w-3 h-3 mt-1 text-orange-600" />
                                                <div>
                                                    <span className="font-medium text-zinc-600 dark:text-zinc-400">Kode Paket:</span>
                                                    <p className="text-zinc-900 dark:text-white font-mono">
                                                        {membership.membership_package.code}
                                                    </p>
                                                </div>
                                            </div>
                                        )}

                                        <div className="flex items-start gap-3 p-2 rounded bg-white dark:bg-zinc-700">
                                            <Clock className="w-3 h-3 mt-1 text-orange-600" />
                                            <div>
                                                <span className="font-medium text-zinc-600 dark:text-zinc-400">Durasi:</span>
                                                <p className="text-zinc-900 dark:text-white">{membership.membership_package.duration} hari</p>
                                            </div>
                                        </div>

                                        <div className="flex items-start gap-3 p-2 rounded bg-white dark:bg-zinc-700">
                                            <CreditCard className="w-3 h-3 mt-1 text-green-600" />
                                            <div>
                                                <span className="font-medium text-zinc-600 dark:text-zinc-400">Harga:</span>
                                                <p className="text-zinc-900 dark:text-white font-semibold">
                                                    Rp {membership.membership_package.price.toLocaleString()}
                                                </p>
                                            </div>
                                        </div>

                                        {membership.membership_package.description && (
                                            <div className="flex items-start gap-3 p-2 rounded bg-white dark:bg-zinc-700">
                                                <FileText className="w-3 h-3 mt-1 text-orange-600" />
                                                <div>
                                                    <span className="font-medium text-zinc-600 dark:text-zinc-400">Deskripsi:</span>
                                                    <p className="text-zinc-900 dark:text-white text-xs leading-relaxed">
                                                        {membership.membership_package.description}
                                                    </p>
                                                </div>
                                            </div>
                                        )}

                                        <div className="flex items-start gap-3 p-2 rounded bg-white dark:bg-zinc-700">
                                            {membership.membership_package.status === "active" ? (
                                                <CheckCircle className="w-3 h-3 mt-1 text-green-600" />
                                            ) : (
                                                <AlertCircle className="w-3 h-3 mt-1 text-red-600" />
                                            )}
                                            <div>
                                                <span className="font-medium text-zinc-600 dark:text-zinc-400">Status Paket:</span>
                                                <p className="text-zinc-900 dark:text-white">
                                                    {membership.membership_package.status === "active" ? "Aktif" : "Tidak Aktif"}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                    <User className="w-4 h-4 mt-1 text-zinc-600" />
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">ID User</span>
                                        <p className="text-zinc-900 dark:text-white">{membership.user_id}</p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                    <Calendar className="w-4 h-4 mt-1 text-zinc-600" />
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Tanggal Dibuat</span>
                                        <p className="text-zinc-900 dark:text-white">
                                            {format(parseISO(membership.created_at), "HH:mm:ss dd-MM-yyyy")}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                    <Calendar className="w-4 h-4 mt-1 text-zinc-600" />
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Terakhir Diupdate</span>
                                        <p className="text-zinc-900 dark:text-white">
                                            {format(parseISO(membership.updated_at), "HH:mm:ss dd-MM-yyyy")}
                                        </p>
                                    </div>
                                </div>

                                {membership.deleted_at && (
                                    <div className="flex items-start gap-3 p-3 rounded-lg bg-red-50 dark:bg-zinc-800 border border-red-200 dark:border-red-800">
                                        <AlertCircle className="w-4 h-4 mt-1 text-red-600" />
                                        <div>
                                            <span className="font-medium text-red-700 dark:text-red-300">Tanggal Dihapus</span>
                                            <p className="text-red-900 dark:text-red-100">
                                                {format(parseISO(membership.deleted_at), "HH:mm:ss dd-MM-yyyy")}
                                            </p>
                                        </div>
                                    </div>
                                )}
                            </div>

                            <div className="mt-6 flex flex-col gap-3">
                                <Button
                                    variant="outline"
                                    onClick={() => setOpenModal(false)}
                                    className="border-orange-200 hover:bg-orange-50 dark:border-orange-800 dark:hover:bg-orange-950"
                                >
                                    Tutup
                                </Button>
                            </div>
                        </div>
                    </div>
                )}
            </>
        )
    }

    return [
        {
            header: "#",
            cell: ({ row }) => (
                <div className="w-12 text-center font-medium text-zinc-600 dark:text-zinc-400">{row.index + 1}</div>
            ),
        },
        {
            accessorKey: "code",
            header: () => <div className="flex w-[5rem] justify-center text-center">Kode Membership</div>,
            cell: ({ row }) => (
                <div className="flex w-[5rem] justify-center text-center">
          <span className="font-mono text-sm bg-zinc-50 dark:bg-zinc-700 px-2 py-1 rounded">
            {row.getValue("code") || "N/A"}
          </span>
                </div>
            ),
            filterFn: (row, id, value) => {
                const cellValue = row.getValue(id) as string | null
                if (!cellValue) return false
                return cellValue.toLowerCase().includes(value.toLowerCase())
            },
        },
        {
            accessorKey: "start_date",
            enableColumnFilter: true,
            filterFn: (row, columnId, filterValue) => {
                const rowDate = new Date(row.getValue(columnId))
                const start = new Date(filterValue.start)
                const end = filterValue.end ? new Date(filterValue.end) : start

                start.setHours(0, 0, 0, 0)
                end.setHours(23, 59, 59, 999)
                rowDate.setHours(12, 0, 0, 0)

                return rowDate >= start && rowDate <= end
            },
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}
                    className="flex w-[4rem] justify-center text-center"
                >
                    Tanggal Mulai
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            ),
            cell: ({ row }) => {
                const dateRaw = row.getValue("start_date") as string
                const date = parseISO(dateRaw)
                const formatted = format(date, "dd-MM-yyyy")

                return <div className="flex w-[4rem] justify-center text-center font-medium">{formatted}</div>
            },
        },
        {
            accessorKey: "end_date",
            enableColumnFilter: true,
            filterFn: (row, columnId, filterValue) => {
                const rowDate = new Date(row.getValue(columnId))
                const start = new Date(filterValue.start)
                const end = filterValue.end ? new Date(filterValue.end) : start

                start.setHours(0, 0, 0, 0)
                end.setHours(23, 59, 59, 999)
                rowDate.setHours(12, 0, 0, 0)

                return rowDate >= start && rowDate <= end
            },
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}
                    className="flex w-[4rem] justify-center text-center"
                >
                    Tanggal Akhir
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            ),
            cell: ({ row }) => {
                const dateRaw = row.getValue("end_date") as string
                const date = parseISO(dateRaw)
                const formatted = format(date, "dd-MM-yyyy")

                return <div className="flex w-[4rem] justify-center text-center font-medium">{formatted}</div>
            },
        },
        {
            accessorKey: "status",
            enableColumnFilter: true,
            header: () => <div className="text-center w-[4rem]">Status</div>,
            cell: ({ row }) => {
                const status = row.getValue("status") as "active" | "expired"

                const statusConfig = {
                    active: {
                        label: "Aktif",
                        color: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
                        icon: CheckCircle,
                    },
                    expired: {
                        label: "Kadaluarsa",
                        color: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
                        icon: AlertCircle,
                    },
                }[status]

                const Icon = statusConfig.icon

                return (
                    <div className="flex w-[4rem] justify-center">
            <span
                className={`inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${statusConfig.color}`}
            >
              <Icon className="h-3 w-3" />
                {statusConfig.label}
            </span>
                    </div>
                )
            },
            filterFn: (row, id, value) => {
                const cellValue = row.getValue(id) as string
                return cellValue === value
            },
        },
        {
            accessorFn: (row) => row.membership_package.name,
            id: "membership_package_name",
            header: () => <div className="flex w-[5rem] justify-center text-center">Paket Membership</div>,
            cell: ({ row }) => (
                <div className="w-[5rem] text-center">
                    <div className="truncate font-medium" title={row.getValue("membership_package_name")}>
                        {row.getValue("membership_package_name")}
                    </div>
                </div>
            ),
            filterFn: (row, id, value) => {
                const cellValue = row.getValue(id) as string
                return cellValue.toLowerCase().includes(value.toLowerCase())
            },
        },
        {
            id: "detail",
            header: () => <div className="text-center w-[6rem]">Detail</div>,
            cell: DetailCell,
        },
    ]
}
