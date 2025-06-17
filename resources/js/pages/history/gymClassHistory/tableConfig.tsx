"use client"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import type { GymClassHistory, SimpleOption, AlertMessage } from "@/types"
import type { ColumnDef } from "@tanstack/react-table"
import { format, parseISO } from "date-fns"
import { id } from "date-fns/locale"
import {
    Calendar,
    Clock,
    Info,
    X,
    User,
    CheckCircle,
    AlertCircle,
    Dumbbell,
    Timer,
    ImageIcon,
    Code,
    ArrowUpDown,
} from "lucide-react"

export const gymClassColumnLabels: Record<string, string> = {
    date: "Tanggal",
    start_time: "Waktu Mulai",
    end_time: "Waktu Akhir",
    class_name: "Nama Kelas",
    status: "Status",
    details: "Detail",
}

export const gymClassStatusOptions: SimpleOption[] = [
    { id: 1, name: "Assigned" },
    { id: 2, name: "Attended" },
    { id: 3, name: "Missed" },
]

export const getGymClassColumns = (setAlertMessage?: (message: AlertMessage) => void): ColumnDef<GymClassHistory>[] => {
    const DetailCell = ({ row }: { row: { original: GymClassHistory } }) => {
        const [openModal, setOpenModal] = useState(false)
        const gymClass = row.original

        return (
            <>
                <div className="flex justify-center w-[5rem]">
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
                                        <Dumbbell className="w-5 h-5" />
                                    </div>
                                    <h2 className="text-xl font-semibold">Detail Kelas Gym</h2>
                                </div>
                                <div className="h-1 bg-gradient-to-r from-orange-500 to-red-500 rounded-full"></div>
                            </div>

                            <div className="text-sm space-y-4">
                                {/* Status Section */}
                                <div className="flex items-start gap-3 p-3 rounded-lg bg-red-50 dark:bg-zinc-800">
                                    {gymClass.status === "attended" ? (
                                        <CheckCircle className="w-4 h-4 mt-1 text-green-600" />
                                    ) : gymClass.status === "assigned" ? (
                                        <Clock className="w-4 h-4 mt-1 text-yellow-600" />
                                    ) : (
                                        <AlertCircle className="w-4 h-4 mt-1 text-red-600" />
                                    )}
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Status Kehadiran</span>
                                        <p className="text-zinc-900 dark:text-white">
                                            {{
                                                attended: "Hadir",
                                                assigned: "Ditugaskan",
                                                missed: "Tidak Hadir",
                                            }[gymClass.status] || gymClass.status}
                                        </p>
                                    </div>
                                </div>

                                {/* Class Info Section */}
                                <div className="p-3 rounded-lg bg-gradient-to-r from-orange-50 to-red-50 dark:from-zinc-800 dark:to-zinc-800 border-l-4 border-orange-500">
                                    <div className="flex items-center gap-2 mb-3">
                                        <Info className="w-4 h-4 text-orange-600" />
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Informasi Kelas</span>
                                    </div>
                                    <div className="ml-6 space-y-2 text-sm">
                                        <div className="flex items-start gap-3 p-2 rounded bg-white dark:bg-zinc-700">
                                            <Dumbbell className="w-3 h-3 mt-1 text-orange-600" />
                                            <div>
                                                <span className="font-medium text-zinc-600 dark:text-zinc-400">Nama Kelas:</span>
                                                <p className="text-zinc-900 dark:text-white">{gymClass.gym_class_schedule.gym_class.name}</p>
                                            </div>
                                        </div>

                                        <div className="flex items-start gap-3 p-2 rounded bg-white dark:bg-zinc-700">
                                            <Code className="w-3 h-3 mt-1 text-orange-600" />
                                            <div>
                                                <span className="font-medium text-zinc-600 dark:text-zinc-400">Kode Kelas:</span>
                                                <p className="text-zinc-900 dark:text-white font-mono">
                                                    {gymClass.gym_class_schedule.gym_class.code}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Schedule Section */}
                                <div className="flex items-start gap-3 p-3 rounded-lg bg-yellow-50 dark:bg-zinc-800">
                                    <Calendar className="w-4 h-4 mt-1 text-yellow-600" />
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Jadwal Kelas</span>
                                        <p className="text-zinc-900 dark:text-white">
                                            {format(parseISO(gymClass.gym_class_schedule.date), "EEEE, dd MMMM yyyy", { locale: id })}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3 p-3 rounded-lg bg-orange-50 dark:bg-zinc-800">
                                    <Timer className="w-4 h-4 mt-1 text-orange-600" />
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Waktu Kelas</span>
                                        <div className="space-y-1">
                                            <p className="text-zinc-900 dark:text-white">
                                                <span className="font-medium">Mulai:</span> {gymClass.gym_class_schedule.start_time}
                                            </p>
                                            <p className="text-zinc-900 dark:text-white">
                                                <span className="font-medium">Selesai:</span> {gymClass.gym_class_schedule.end_time}
                                            </p>
                                            <p className="text-zinc-900 dark:text-white">
                                                <span className="font-medium">Durasi:</span>{" "}
                                                {calculateDuration(
                                                    gymClass.gym_class_schedule.start_time,
                                                    gymClass.gym_class_schedule.end_time,
                                                )}{" "}
                                                menit
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {/* Images Section */}
                                {gymClass.gym_class_schedule.gym_class.images &&
                                    gymClass.gym_class_schedule.gym_class.images.length > 0 && (
                                        <div className="p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                            <div className="flex items-center gap-2 mb-3">
                                                <ImageIcon className="w-4 h-4 text-zinc-600" />
                                                <span className="font-medium text-zinc-700 dark:text-zinc-300">Gambar Kelas</span>
                                            </div>
                                            <div className="grid grid-cols-2 gap-2 mt-2">
                                                {gymClass.gym_class_schedule.gym_class.images.map((image, index) => (
                                                    <div
                                                        key={index}
                                                        className="aspect-video rounded-md overflow-hidden bg-zinc-100 dark:bg-zinc-700"
                                                    >
                                                        <img
                                                            src={`storage/${image}`}
                                                            alt={`${gymClass.gym_class_schedule.gym_class.name} - ${index + 1}`}
                                                            className="w-full h-full object-cover"
                                                        />
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                {/* User Info */}
                                <div className="flex items-start gap-3 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                    <User className="w-4 h-4 mt-1 text-zinc-600" />
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">ID User</span>
                                        <p className="text-zinc-900 dark:text-white">{gymClass.user_id}</p>
                                    </div>
                                </div>

                                {/* Created At */}
                                <div className="flex items-start gap-3 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                    <Calendar className="w-4 h-4 mt-1 text-zinc-600" />
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Tanggal Dibuat</span>
                                        <p className="text-zinc-900 dark:text-white">
                                            {format(parseISO(gymClass.created_at), "HH:mm:ss dd-MM-yyyy")}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div className="mt-6 flex flex-col gap-3">
                                {gymClass.status === "assigned" && (
                                    <Button
                                        onClick={() => {
                                            // Handle attendance marking logic here
                                            if (setAlertMessage) {
                                                setAlertMessage({
                                                    message: "Status kehadiran berhasil diperbarui!",
                                                    type: "success",
                                                })
                                            }
                                            setOpenModal(false)
                                        }}
                                        className="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-medium"
                                    >
                                        <CheckCircle className="mr-2 h-4 w-4" />
                                        Tandai Hadir
                                    </Button>
                                )}
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

    // Helper function to calculate duration between two time strings (HH:MM:SS)
    const calculateDuration = (startTime: string, endTime: string): number => {
        const [startHours, startMinutes] = startTime.split(":").map(Number)
        const [endHours, endMinutes] = endTime.split(":").map(Number)

        const startTotalMinutes = startHours * 60 + startMinutes
        const endTotalMinutes = endHours * 60 + endMinutes

        return endTotalMinutes - startTotalMinutes
    }

    return [
        {
            header: "#",
            cell: ({ row }) => (
                <div className="w-12 text-center font-medium text-zinc-600 dark:text-zinc-400">{row.index + 1}</div>
            ),
        },
        {
            accessorFn: (row) => row.gym_class_schedule.date,
            id: "date",
            enableColumnFilter: true,
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}
                    className="flex w-[6rem] justify-center text-center"
                >
                    Tanggal
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            ),
            cell: ({ row }) => {
                const rawDate = row.getValue("date") as string
                const parsed = parseISO(rawDate)
                const formatted = format(parsed, "dd-MM-yyyy")

                return <div className="flex w-[6rem] justify-center text-center font-medium">{formatted}</div>
            },
            filterFn: (row, columnId, filterValue) => {
                const rawDate = row.getValue(columnId) as string
                const rowDate = parseISO(rawDate)
                const start = new Date(filterValue.start)
                const end = filterValue.end ? new Date(filterValue.end) : start

                start.setHours(0, 0, 0, 0)
                end.setHours(23, 59, 59, 999)
                rowDate.setHours(12, 0, 0, 0)

                return rowDate >= start && rowDate <= end
            },
        },
        {
            accessorFn: (row) => row.gym_class_schedule.start_time,
            id: "start_time",
            header: () => <div className="flex w-[5rem] justify-center text-center">Waktu Mulai</div>,
            cell: ({ row }) => (
                <div className="flex w-[5rem] justify-center text-center">
                    <span className="font-mono text-sm">{row.getValue("start_time")}</span>
                </div>
            ),
        },
        {
            accessorFn: (row) => row.gym_class_schedule.end_time,
            id: "end_time",
            header: () => <div className="flex w-[5rem] justify-center text-center">Waktu Selesai</div>,
            cell: ({ row }) => (
                <div className="flex w-[5rem] justify-center text-center">
                    <span className="font-mono text-sm">{row.getValue("end_time")}</span>
                </div>
            ),
        },
        {
            accessorFn: (row) => row.gym_class_schedule.gym_class.name,
            id: "class_name",
            header: () => <div className="flex w-[10rem] justify-center text-center">Nama Kelas</div>,
            cell: ({ row }) => (
                <div className="w-[10rem] text-center">
                    <div className="truncate font-medium" title={row.getValue("class_name")}>
                        {row.getValue("class_name")}
                    </div>
                </div>
            ),
            filterFn: (row, id, value) => {
                const cellValue = row.getValue(id) as string
                return cellValue.toLowerCase().includes(value.toLowerCase())
            },
        },
        {
            accessorKey: "status",
            id: "status",
            header: () => <div className="text-center w-[5rem]">Status</div>,
            cell: ({ row }) => {
                const status = row.getValue("status") as "assigned" | "attended" | "missed"

                const statusConfig = {
                    assigned: {
                        label: "Ditugaskan",
                        color: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
                        icon: Clock,
                    },
                    attended: {
                        label: "Hadir",
                        color: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
                        icon: CheckCircle,
                    },
                    missed: {
                        label: "Tidak Hadir",
                        color: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
                        icon: AlertCircle,
                    },
                }[status]

                const Icon = statusConfig.icon

                return (
                    <div className="flex justify-center w-[5rem]">
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
            id: "details",
            header: () => <div className="flex w-[5rem] justify-center text-center">Aksi</div>,
            cell: DetailCell,
        },
    ]
}
