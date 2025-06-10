"use client"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import type { PaymentHistory, SimpleOption, AlertMessage } from "@/types"
import type { ColumnDef } from "@tanstack/react-table"
import { format, parseISO } from "date-fns"
import {
    CreditCard,
    Info,
    ShoppingCart,
    X,
    Box,
    CalendarDays,
    Banknote,
    Code,
    CheckCircle,
    Clock,
    AlertCircle,
} from "lucide-react"
import { router } from "@inertiajs/react"

export const paymentColumnLabels: Record<string, string> = {
    code: "Kode Pembayaran",
    created_at: "Tanggal Dibuat",
    purchasable_type: "Tipe Pembelian",
    purchasable_name: "Nama Produk",
    amount: "Nominal",
    payment_status: "Status",
    detail: "Detail",
}

export const paymentStatusOptions: SimpleOption[] = [
    { id: 1, name: "Pending" },
    { id: 2, name: "Paid" },
    { id: 3, name: "Failed" },
]

export const purchasableTypeOptions: SimpleOption[] = [
    { id: 1, name: "Paket Membership" },
    { id: 2, name: "Kelas Gym" },
    { id: 3, name: "Paket Personal Trainer" },
]

export const getPaymentColumns = (setAlertMessage: (message: AlertMessage) => void): ColumnDef<PaymentHistory>[] => {
    const DetailCell = ({ row }: { row: { original: PaymentHistory } }) => {
        const [openModal, setOpenModal] = useState(false)
        const payment = row.original

        const handlePayClick = () => {
            if (!payment.snap_token) {
                setAlertMessage({
                    message: "Token pembayaran tidak tersedia.",
                    type: "error",
                })
                return
            }

            window.snap.pay(payment.snap_token, {
                onSuccess: (result) => {
                    router.post("/payments/update-status", {
                        transaction_id: payment.id,
                        status: "paid",
                    })

                    setAlertMessage({
                        message: "Pembayaran berhasil!",
                        type: "success",
                    })
                },
                onPending: (result) => {
                    router.post("/payments/update-status", {
                        transaction_id: payment.id,
                        status: "pending",
                    })
                },
                onError: (result) => {
                    router.post("/payments/update-status", {
                        transaction_id: payment.id,
                        status: "failed",
                    })

                    setAlertMessage({
                        message: "Pembayaran gagal. Silakan coba lagi.",
                        type: "error",
                    })
                },
            })
        }

        return (
            <>
                <div className="flex justify-center w-[6rem]">
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => setOpenModal(true)}
                        className="border-orange-200 hover:bg-orange-50 hover:border-orange-300"
                    >
                        Detail
                    </Button>
                </div>

                {openModal && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center py-8 bg-black/50 dark:bg-black/70 backdrop-blur-sm">
                        <div className="relative bg-white dark:bg-neutral-900 p-6 rounded-2xl shadow-2xl max-w-md w-full text-gray-800 dark:text-gray-100 border border-orange-200">
                            <button
                                onClick={() => setOpenModal(false)}
                                className="absolute top-3 right-3 text-gray-500 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-red-50"
                            >
                                <X className="w-5 h-5" />
                            </button>

                            <div className="mb-6">
                                <div className="flex items-center gap-3 mb-2">
                                    <div className="p-2 rounded-full bg-gradient-to-r from-orange-500 to-red-500 text-white">
                                        <Info className="w-5 h-5" />
                                    </div>
                                    <h2 className="text-xl font-semibold">Detail Transaksi</h2>
                                </div>
                                <div className="h-1 bg-gradient-to-r from-orange-500 to-red-500 rounded-full"></div>
                            </div>

                            <div className="text-sm space-y-4">
                                <div className="flex items-start gap-3 p-3 rounded-lg bg-orange-50 dark:bg-neutral-800">
                                    <CreditCard className="w-4 h-4 mt-1 text-orange-600" />
                                    <div>
                                        <span className="font-medium text-gray-700 dark:text-gray-300">Kode Pembayaran</span>
                                        <p className="text-gray-900 dark:text-white font-mono">{payment.code}</p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3 p-3 rounded-lg bg-green-50 dark:bg-neutral-800">
                                    <Banknote className="w-4 h-4 mt-1 text-green-600" />
                                    <div>
                                        <span className="font-medium text-gray-700 dark:text-gray-300">Nominal</span>
                                        <p className="text-gray-900 dark:text-white font-semibold">Rp {payment.amount.toLocaleString()}</p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3 p-3 rounded-lg bg-blue-50 dark:bg-neutral-800">
                                    {payment.payment_status === "paid" ? (
                                        <CheckCircle className="w-4 h-4 mt-1 text-green-600" />
                                    ) : payment.payment_status === "pending" ? (
                                        <Clock className="w-4 h-4 mt-1 text-yellow-600" />
                                    ) : (
                                        <AlertCircle className="w-4 h-4 mt-1 text-red-600" />
                                    )}
                                    <div>
                                        <span className="font-medium text-gray-700 dark:text-gray-300">Status Pembayaran</span>
                                        <p className="text-gray-900 dark:text-white">
                                            {(
                                                {
                                                    pending: "Menunggu Pembayaran",
                                                    paid: "Berhasil Terbayar",
                                                    failed: "Pembayaran Gagal",
                                                } as Record<string, string>
                                            )[payment.payment_status] ?? payment.payment_status}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3 p-3 rounded-lg bg-purple-50 dark:bg-neutral-800">
                                    <ShoppingCart className="w-4 h-4 mt-1 text-purple-600" />
                                    <div>
                                        <span className="font-medium text-gray-700 dark:text-gray-300">Tipe Pembelian</span>
                                        <p className="text-gray-900 dark:text-white">
                                            {{
                                                membership_package: "Paket Membership",
                                                gym_class: "Kelas Gym",
                                                personal_trainer_package: "Paket Personal Trainer",
                                            }[payment.purchasable_type] ?? payment.purchasable_type}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3 p-3 rounded-lg bg-indigo-50 dark:bg-neutral-800">
                                    <Box className="w-4 h-4 mt-1 text-indigo-600" />
                                    <div>
                                        <span className="font-medium text-gray-700 dark:text-gray-300">Nama Produk</span>
                                        <p className="text-gray-900 dark:text-white">{payment.purchasable_name}</p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3 p-3 rounded-lg bg-teal-50 dark:bg-neutral-800">
                                    <Code className="w-4 h-4 mt-1 text-teal-600" />
                                    <div>
                                        <span className="font-medium text-gray-700 dark:text-gray-300">Kode Produk</span>
                                        <p className="text-gray-900 dark:text-white font-mono">{payment.purchasable_code}</p>
                                    </div>
                                </div>

                                {payment.gym_class_schedule && (
                                    <div className="p-3 rounded-lg bg-amber-50 dark:bg-neutral-800 border-l-4 border-amber-500">
                                        <div className="flex items-center gap-2 mb-2">
                                            <CalendarDays className="w-4 h-4 text-amber-600" />
                                            <span className="font-medium text-gray-700 dark:text-gray-300">Jadwal Kelas Gym</span>
                                        </div>
                                        <div className="ml-6 space-y-1 text-sm">
                                            <p>
                                                <span className="font-medium">Tanggal:</span> {payment.gym_class_schedule.date}
                                            </p>
                                            <p>
                                                <span className="font-medium">Mulai:</span> {payment.gym_class_schedule.start_time}
                                            </p>
                                            <p>
                                                <span className="font-medium">Selesai:</span> {payment.gym_class_schedule.end_time}
                                            </p>
                                        </div>
                                    </div>
                                )}

                                <div className="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-neutral-800">
                                    <CalendarDays className="w-4 h-4 mt-1 text-gray-600" />
                                    <div>
                                        <span className="font-medium text-gray-700 dark:text-gray-300">Tanggal Pembayaran</span>
                                        <p className="text-gray-900 dark:text-white">
                                            {payment.payment_date
                                                ? format(parseISO(payment.payment_date), "HH:mm:ss dd-MM-yyyy")
                                                : "Belum dibayar"}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div className="mt-6 flex flex-col gap-3">
                                {payment.snap_token && payment.payment_status !== "paid" && (
                                    <Button
                                        onClick={handlePayClick}
                                        className="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-medium"
                                    >
                                        <CreditCard className="mr-2 h-4 w-4" />
                                        Bayar Sekarang
                                    </Button>
                                )}
                                <Button
                                    variant="outline"
                                    onClick={() => setOpenModal(false)}
                                    className="border-orange-200 hover:bg-orange-50"
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
                <div className="w-12 text-center font-medium text-gray-600 dark:text-gray-400">{row.index + 1}</div>
            ),
        },
        {
            accessorKey: "code",
            header: () => <div className="text-center w-[8rem]">Kode Pembayaran</div>,
            cell: ({ row }) => (
                <div className="text-center w-[8rem] font-mono text-sm bg-gray-50 dark:bg-neutral-700 px-2 py-1 rounded">
                    {row.getValue("code")}
                </div>
            ),
            filterFn: (row, id, value) => {
                const cellValue = row.getValue(id) as string
                return cellValue.toLowerCase().includes(value.toLowerCase())
            },
        },
        {
            accessorKey: "created_at",
            header: () => <div className="text-center w-[8rem]">Tanggal Dibuat</div>,
            cell: ({ row }) => {
                const dateRaw = row.getValue("created_at") as string | null
                if (!dateRaw) return <div className="text-center w-[8rem] text-gray-400">-</div>

                const date = parseISO(dateRaw)
                const formatted = format(date, "dd-MM-yyyy")
                return <div className="text-center w-[8rem] font-medium">{formatted}</div>
            },
            filterFn: (row, id, value) => {
                const dateRaw = row.getValue(id) as string | null
                if (!dateRaw || !value?.start) return true

                const rowDate = parseISO(dateRaw)
                const startDate = new Date(value.start)
                const endDate = value.end ? new Date(value.end) : startDate

                // Set time to start/end of day for proper comparison
                startDate.setHours(0, 0, 0, 0)
                endDate.setHours(23, 59, 59, 999)

                return rowDate >= startDate && rowDate <= endDate
            },
        },
        {
            accessorKey: "purchasable_type",
            header: () => <div className="text-center w-[10rem]">Tipe Pembelian</div>,
            cell: ({ row }) => {
                const type = row.getValue("purchasable_type") as "membership_package" | "gym_class" | "personal_trainer_package"

                const mapping: Record<
                    "membership_package" | "gym_class" | "personal_trainer_package",
                    { label: string; color: string }
                > = {
                    membership_package: {
                        label: "Paket Membership",
                        color: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
                    },
                    gym_class: { label: "Kelas Gym", color: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200" },
                    personal_trainer_package: {
                        label: "Personal Trainer",
                        color: "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200",
                    },
                }

                const config = mapping[type] || { label: type, color: "bg-gray-100 text-gray-800" }

                return (
                    <div className="flex justify-center w-[10rem]">
                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${config.color}`}>{config.label}</span>
                    </div>
                )
            },
            filterFn: (row, id, value) => {
                const cellValue = row.getValue(id) as string
                return cellValue === value
            },
        },
        {
            accessorKey: "purchasable_name",
            header: () => <div className="text-center w-[12rem]">Nama Produk</div>,
            cell: ({ row }) => (
                <div className="w-[12rem] text-center">
                    <div className="truncate font-medium" title={row.getValue("purchasable_name")}>
                        {row.getValue("purchasable_name")}
                    </div>
                </div>
            ),
            filterFn: (row, id, value) => {
                const cellValue = row.getValue(id) as string
                return cellValue.toLowerCase().includes(value.toLowerCase())
            },
        },
        {
            accessorKey: "amount",
            header: () => <div className="text-center w-[8rem]">Nominal</div>,
            cell: ({ row }) => {
                const amount = row.getValue("amount") as number
                return (
                    <div className="text-center w-[8rem]">
                        <span className="font-semibold text-green-600 dark:text-green-400">Rp {amount.toLocaleString()}</span>
                    </div>
                )
            },
        },
        {
            accessorKey: "payment_status",
            header: () => <div className="text-center w-[8rem]">Status</div>,
            cell: ({ row }) => {
                const status = row.getValue("payment_status") as string

                const statusConfig = {
                    paid: {
                        label: "Terbayar",
                        color: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
                        icon: CheckCircle,
                    },
                    pending: {
                        label: "Pending",
                        color: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
                        icon: Clock,
                    },
                    failed: {
                        label: "Gagal",
                        color: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
                        icon: AlertCircle,
                    },
                }[status.toLowerCase()] || {
                    label: status,
                    color: "bg-gray-100 text-gray-800",
                    icon: AlertCircle,
                }

                const Icon = statusConfig.icon

                return (
                    <div className="flex justify-center w-[8rem]">
            <span
                className={`inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${statusConfig.color}`}
            >
              <Icon className="h-3 w-3" />
                {statusConfig.label}
            </span>
                    </div>
                )
            },
        },
        {
            id: "detail",
            header: () => <div className="text-center w-[6rem]">Detail</div>,
            cell: DetailCell,
        },
    ]
}
