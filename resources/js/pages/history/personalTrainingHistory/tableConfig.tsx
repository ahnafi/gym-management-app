"use client"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import type { PersonalTrainingHistory, SimpleOption, AlertMessage } from "@/types"
import type { ColumnDef } from "@tanstack/react-table"
import { format, parseISO } from "date-fns"
import { id } from "date-fns/locale"
import {
    Calendar,
    Clock,
    X,
    User,
    CheckCircle,
    AlertCircle,
    Package,
    Timer,
    ImageIcon,
    Code,
    ArrowUpDown,
    UserCheck,
    Activity,
    Target,
    Users,
} from "lucide-react"

export const personalTrainingColumnLabels: Record<string, string> = {
    package_code: "Kode Paket",
    package_name: "Nama Paket",
    trainer_nickname: "Trainer",
    start_date: "Tanggal Mulai",
    end_date: "Tanggal Berakhir",
    status: "Status",
    detail: "Detail",
}

export const personalTrainingStatusOptions: SimpleOption[] = [
    { id: 1, name: "Active" },
    { id: 2, name: "Cancelled" },
    { id: 3, name: "Completed" },
]

export const getPersonalTrainingColumns = (
    setAlertMessage?: (message: AlertMessage) => void,
): ColumnDef<PersonalTrainingHistory>[] => {
    const DetailCell = ({ row }: { row: { original: PersonalTrainingHistory } }) => {
        const [openModal, setOpenModal] = useState(false)
        const training = row.original

        const formatTime = (timeString: string | null) => {
            if (!timeString) return "Belum tercatat"
            try {
                return format(parseISO(timeString), "HH:mm:ss dd-MM-yyyy")
            } catch {
                return timeString
            }
        }

        return (
            <>
                <div className="flex justify-center">
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
                        <div className="relative bg-white dark:bg-zinc-900 p-6 rounded-2xl shadow-2xl max-w-lg w-full text-zinc-800 dark:text-zinc-100 border border-orange-200 dark:border-orange-800 max-h-[90vh] overflow-y-auto">
                            <button
                                onClick={() => setOpenModal(false)}
                                className="absolute top-3 right-3 text-zinc-500 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-950"
                            >
                                <X className="w-5 h-5" />
                            </button>

                            <div className="mb-6">
                                <div className="flex items-center gap-3 mb-2">
                                    <div className="p-2 rounded-full bg-gradient-to-r from-orange-500 to-red-500 text-white">
                                        <Users className="w-5 h-5" />
                                    </div>
                                    <h2 className="text-xl font-semibold">Detail Personal Training</h2>
                                </div>
                                <div className="h-1 bg-gradient-to-r from-orange-500 to-red-500 rounded-full"></div>
                            </div>

                            <div className="text-sm space-y-4">
                                {/* Status Section */}
                                <div className="flex items-start gap-3 p-3 rounded-lg bg-red-50 dark:bg-zinc-800">
                                    {training.status === "completed" ? (
                                        <CheckCircle className="w-4 h-4 mt-1 text-green-600" />
                                    ) : training.status === "active" ? (
                                        <Activity className="w-4 h-4 mt-1 text-blue-600" />
                                    ) : (
                                        <AlertCircle className="w-4 h-4 mt-1 text-red-600" />
                                    )}
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Status Training</span>
                                        <p className="text-zinc-900 dark:text-white">
                                            {{
                                                active: "Aktif",
                                                cancelled: "Dibatalkan",
                                                completed: "Selesai",
                                            }[training.status] || training.status}
                                        </p>
                                    </div>
                                </div>

                                {/* Days Left */}
                                {training.status === "active" && (
                                    <div className="flex items-start gap-3 p-3 rounded-lg bg-yellow-50 dark:bg-zinc-800">
                                        <Target className="w-4 h-4 mt-1 text-yellow-600" />
                                        <div>
                                            <span className="font-medium text-zinc-700 dark:text-zinc-300">Sisa Hari</span>
                                            <p className="text-zinc-900 dark:text-white font-semibold">{training.day_left} hari tersisa</p>
                                        </div>
                                    </div>
                                )}

                                {/* Training Period */}
                                <div className="flex items-start gap-3 p-3 rounded-lg bg-orange-50 dark:bg-zinc-800">
                                    <Calendar className="w-4 h-4 mt-1 text-orange-600" />
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Periode Training</span>
                                        <div className="space-y-1">
                                            <p className="text-zinc-900 dark:text-white">
                                                <span className="font-medium">Mulai:</span>{" "}
                                                {format(parseISO(training.start_date), "dd MMMM yyyy", { locale: id })}
                                            </p>
                                            <p className="text-zinc-900 dark:text-white">
                                                <span className="font-medium">Berakhir:</span>{" "}
                                                {format(parseISO(training.end_date), "dd MMMM yyyy", { locale: id })}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {/* Package Info Section */}
                                <div className="p-3 rounded-lg bg-gradient-to-r from-orange-50 to-red-50 dark:from-zinc-800 dark:to-zinc-800 border-l-4 border-orange-500">
                                    <div className="flex items-center gap-2 mb-3">
                                        <Package className="w-4 h-4 text-orange-600" />
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Detail Paket Training</span>
                                    </div>
                                    <div className="ml-6 space-y-2 text-sm">
                                        <div className="flex items-start gap-3 p-2 rounded bg-white dark:bg-zinc-700">
                                            <Package className="w-3 h-3 mt-1 text-orange-600" />
                                            <div>
                                                <span className="font-medium text-zinc-600 dark:text-zinc-400">Nama Paket:</span>
                                                <p className="text-zinc-900 dark:text-white">{training.personal_trainer_package.name}</p>
                                            </div>
                                        </div>

                                        <div className="flex items-start gap-3 p-2 rounded bg-white dark:bg-zinc-700">
                                            <Code className="w-3 h-3 mt-1 text-orange-600" />
                                            <div>
                                                <span className="font-medium text-zinc-600 dark:text-zinc-400">Kode Paket:</span>
                                                <p className="text-zinc-900 dark:text-white font-mono">
                                                    {training.personal_trainer_package.code}
                                                </p>
                                            </div>
                                        </div>

                                        <div className="flex items-start gap-3 p-2 rounded bg-white dark:bg-zinc-700">
                                            <Timer className="w-3 h-3 mt-1 text-orange-600" />
                                            <div>
                                                <span className="font-medium text-zinc-600 dark:text-zinc-400">Durasi:</span>
                                                <p className="text-zinc-900 dark:text-white">
                                                    {training.personal_trainer_package.day_duration} hari
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Package Images */}
                                    {training.personal_trainer_package.images && training.personal_trainer_package.images.length > 0 && (
                                        <div className="mt-3 ml-6">
                                            <div className="flex items-center gap-2 mb-2">
                                                <ImageIcon className="w-3 h-3 text-orange-600" />
                                                <span className="font-medium text-zinc-600 dark:text-zinc-400">Gambar Paket:</span>
                                            </div>
                                            <div className="grid grid-cols-2 gap-2">
                                                {training.personal_trainer_package.images.map((image, index) => (
                                                    <div
                                                        key={index}
                                                        className="aspect-video rounded-md overflow-hidden bg-zinc-100 dark:bg-zinc-700"
                                                    >
                                                        <img
                                                            src={`/storage/${image}`}
                                                            alt={`${training.personal_trainer_package.name} - ${index + 1}`}
                                                            className="w-full h-full object-cover"
                                                        />
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                </div>

                                {/* Trainer Info Section */}
                                <div className="p-3 rounded-lg bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-zinc-800 dark:to-zinc-800 border-l-4 border-yellow-500">
                                    <div className="flex items-center gap-2 mb-3">
                                        <UserCheck className="w-4 h-4 text-yellow-600" />
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Informasi Trainer</span>
                                    </div>
                                    <div className="ml-6 space-y-2 text-sm">
                                        <div className="flex items-start gap-3 p-2 rounded bg-white dark:bg-zinc-700">
                                            <User className="w-3 h-3 mt-1 text-yellow-600" />
                                            <div>
                                                <span className="font-medium text-zinc-600 dark:text-zinc-400">Nama:</span>
                                                <p className="text-zinc-900 dark:text-white">
                                                    Coach {training.personal_trainer_package.personal_trainer.nickname}
                                                </p>
                                            </div>
                                        </div>

                                        <div className="flex items-start gap-3 p-2 rounded bg-white dark:bg-zinc-700">
                                            <Code className="w-3 h-3 mt-1 text-yellow-600" />
                                            <div>
                                                <span className="font-medium text-zinc-600 dark:text-zinc-400">Kode Trainer:</span>
                                                <p className="text-zinc-900 dark:text-white font-mono">
                                                    {training.personal_trainer_package.personal_trainer.code}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Trainer Images */}
                                    {training.personal_trainer_package.personal_trainer.images &&
                                        training.personal_trainer_package.personal_trainer.images.length > 0 && (
                                            <div className="mt-3 ml-6">
                                                <div className="flex items-center gap-2 mb-2">
                                                    <ImageIcon className="w-3 h-3 text-yellow-600" />
                                                    <span className="font-medium text-zinc-600 dark:text-zinc-400">Foto Trainer:</span>
                                                </div>
                                                <div className="grid grid-cols-2 gap-2">
                                                    {training.personal_trainer_package.personal_trainer.images.map((image, index) => (
                                                        <div
                                                            key={index}
                                                            className="aspect-square rounded-md overflow-hidden bg-zinc-100 dark:bg-zinc-700"
                                                        >
                                                            <img
                                                                src={`/storage/${image}`}
                                                                alt={`Coach ${training.personal_trainer_package.personal_trainer.nickname} - ${index + 1}`}
                                                                className="w-full h-full object-cover"
                                                            />
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        )}
                                </div>

                                {/* Training Schedules */}
                                {training.personal_trainer_schedules && training.personal_trainer_schedules.length > 0 && (
                                    <div className="p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                        <div className="flex items-center gap-2 mb-3">
                                            <Clock className="w-4 h-4 text-zinc-600" />
                                            <span className="font-medium text-zinc-700 dark:text-zinc-300">
                        Riwayat Jadwal ({training.personal_trainer_schedules.length} sesi)
                      </span>
                                        </div>
                                        <div className="space-y-2 max-h-40 overflow-y-auto">
                                            {training.personal_trainer_schedules.map((schedule, index) => (
                                                <div key={schedule.id} className="p-2 rounded bg-white dark:bg-zinc-700 text-xs">
                                                    <div className="flex items-center gap-2 mb-1">
                                                        <span className="font-medium text-zinc-600 dark:text-zinc-400">Sesi {index + 1}:</span>
                                                        <span className="text-zinc-900 dark:text-white">
                              {format(parseISO(schedule.scheduled_at), "dd MMM yyyy HH:mm")}
                            </span>
                                                    </div>
                                                    <div className="ml-4 space-y-1">
                                                        <p className="text-zinc-700 dark:text-zinc-300">
                                                            <span className="font-medium">Check-in:</span> {formatTime(schedule.check_in_time)}
                                                        </p>
                                                        <p className="text-zinc-700 dark:text-zinc-300">
                                                            <span className="font-medium">Check-out:</span> {formatTime(schedule.check_out_time)}
                                                        </p>
                                                    </div>
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
                                        <p className="text-zinc-900 dark:text-white">{training.user_id}</p>
                                    </div>
                                </div>

                                {/* Timestamps */}
                                <div className="flex items-start gap-3 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                    <Calendar className="w-4 h-4 mt-1 text-zinc-600" />
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Tanggal Dibuat</span>
                                        <p className="text-zinc-900 dark:text-white">
                                            {format(parseISO(training.created_at), "HH:mm:ss dd-MM-yyyy")}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                    <Calendar className="w-4 h-4 mt-1 text-zinc-600" />
                                    <div>
                                        <span className="font-medium text-zinc-700 dark:text-zinc-300">Terakhir Diupdate</span>
                                        <p className="text-zinc-900 dark:text-white">
                                            {format(parseISO(training.updated_at), "HH:mm:ss dd-MM-yyyy")}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div className="mt-6 flex flex-col gap-3">
                                {training.status === "active" && (
                                    <Button
                                        onClick={() => {
                                            // Handle training completion or other actions
                                            if (setAlertMessage) {
                                                setAlertMessage({
                                                    message: "Aksi berhasil dijalankan!",
                                                    type: "success",
                                                })
                                            }
                                            setOpenModal(false)
                                        }}
                                        className="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-medium"
                                    >
                                        <Activity className="mr-2 h-4 w-4" />
                                        Kelola Training
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

    return [
        {
            header: "#",
            cell: ({ row }) => (
                <div className="w-12 text-center font-medium text-zinc-600 dark:text-zinc-400">{row.index + 1}</div>
            ),
        },
        {
            accessorFn: (row) => row.personal_trainer_package?.code ?? "-",
            id: "package_code",
            header: () => <div className="text-center w-[6rem]">Kode Paket</div>,
            cell: ({ row }) => (
                <div className="text-center w-[6rem]">
          <span className="font-mono text-sm bg-zinc-50 dark:bg-zinc-700 px-2 py-1 rounded">
            {row.getValue("package_code")}
          </span>
                </div>
            ),
            filterFn: (row, id, value) => {
                const cellValue = row.getValue(id) as string
                return cellValue.toLowerCase().includes(value.toLowerCase())
            },
        },
        {
            accessorFn: (row) => row.personal_trainer_package?.name ?? "-",
            id: "package_name",
            header: () => <div className="text-center w-[8rem]">Nama Paket</div>,
            cell: ({ row }) => (
                <div className="w-[8rem] text-center">
                    <div className="truncate font-medium" title={row.getValue("package_name")}>
                        {row.getValue("package_name")}
                    </div>
                </div>
            ),
            filterFn: (row, id, value) => {
                const cellValue = row.getValue(id) as string
                return cellValue.toLowerCase().includes(value.toLowerCase())
            },
        },
        {
            accessorFn: (row) => row.personal_trainer_package?.personal_trainer?.nickname ?? "-",
            id: "trainer_nickname",
            header: () => <div className="text-center w-[6rem]">Trainer</div>,
            cell: ({ row }) => (
                <div className="text-center w-[6rem] capitalize">Coach {row.getValue("trainer_nickname")}</div>
            ),
            filterFn: (row, id, value) => {
                const cellValue = row.getValue(id) as string
                return cellValue.toLowerCase().includes(value.toLowerCase())
            },
        },
        {
            accessorKey: "start_date",
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}
                    className="text-center w-[6rem]"
                >
                    Tanggal Mulai
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            ),
            cell: ({ row }) => {
                const date = parseISO(row.getValue("start_date"))
                return <div className="text-center w-[6rem] font-medium">{format(date, "dd-MM-yyyy")}</div>
            },
            filterFn: (row, columnId, filterValue) => {
                const dateRaw = row.getValue(columnId) as string
                const rowDate = parseISO(dateRaw)
                const start = new Date(filterValue.start)
                const end = filterValue.end ? new Date(filterValue.end) : start

                start.setHours(0, 0, 0, 0)
                end.setHours(23, 59, 59, 999)
                rowDate.setHours(12, 0, 0, 0)

                return rowDate >= start && rowDate <= end
            },
        },
        {
            accessorKey: "end_date",
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}
                    className="text-center w-[6rem]"
                >
                    Tanggal Berakhir
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            ),
            cell: ({ row }) => {
                const date = parseISO(row.getValue("end_date"))
                return <div className="text-center w-[6rem] font-medium">{format(date, "dd-MM-yyyy")}</div>
            },
            filterFn: (row, columnId, filterValue) => {
                const dateRaw = row.getValue(columnId) as string
                const rowDate = parseISO(dateRaw)
                const start = new Date(filterValue.start)
                const end = filterValue.end ? new Date(filterValue.end) : start

                start.setHours(0, 0, 0, 0)
                end.setHours(23, 59, 59, 999)
                rowDate.setHours(12, 0, 0, 0)

                return rowDate >= start && rowDate <= end
            },
        },
        {
            accessorKey: "status",
            header: () => <div className="text-center w-[6rem]">Status</div>,
            cell: ({ row }) => {
                const status = row.getValue("status") as "active" | "cancelled" | "completed"

                const statusConfig = {
                    active: {
                        label: "Aktif",
                        color: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
                        icon: Activity,
                    },
                    cancelled: {
                        label: "Dibatalkan",
                        color: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
                        icon: AlertCircle,
                    },
                    completed: {
                        label: "Selesai",
                        color: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
                        icon: CheckCircle,
                    },
                }[status]

                const Icon = statusConfig.icon

                return (
                    <div className="text-center w-[6rem]">
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
            id: "detail",
            header: () => <div className="text-center">Detail</div>,
            cell: DetailCell,
        },
    ]
}
