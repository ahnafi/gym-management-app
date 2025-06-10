import AppLayout from "@/layouts/app-layout"
import type { BreadcrumbItem, DashboardProps } from "@/types"
import { Head } from "@inertiajs/react"
import {
    CalendarDays,
    Timer,
    Dumbbell,
    Clock,
    MapPin,
    Calendar,
    CheckCircle,
    XCircle,
    AlertCircle,
    CreditCard,
    Activity,
    TrendingUp,
    Flame,
} from "lucide-react"

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Dashboard",
        href: "/dashboard",
    },
]

export default function Dashboard({ summary, data }: DashboardProps) {
    const summaryCards = [
        {
            title: "Visit Gym Bulan Ini",
            value: summary.visitCountInCurrentMonth,
            icon: <CalendarDays className="text-red-600 dark:text-red-400 size-6" />,
            bgColor: "bg-red-50 dark:bg-red-950/30",
        },
        {
            title: "Visit Gym Minggu Ini",
            value: summary.visitCountInCurrentWeek,
            icon: <TrendingUp className="text-orange-600 dark:text-orange-400 size-6" />,
            bgColor: "bg-orange-50 dark:bg-orange-950/30",
        },
        {
            title: "Kelas Gym Bulan Ini",
            value: summary.gymClassCountInCurrentMonth,
            icon: <Dumbbell className="text-amber-600 dark:text-amber-400 size-6" />,
            bgColor: "bg-amber-50 dark:bg-amber-950/30",
        },
        {
            title: "Rata-rata Waktu Visit",
            value: summary.averageVisitTimeFormatted ?? "Belum Ada",
            icon: <Timer className="text-yellow-600 dark:text-yellow-400 size-6" />,
            bgColor: "bg-yellow-50 dark:bg-yellow-950/30",
        },
    ]

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString("id-ID", {
            day: "numeric",
            month: "short",
            year: "numeric",
        })
    }

    const formatTime = (timeString: string) => {
        return timeString.slice(0, 5) // HH:mm
    }

    const getStatusIcon = (status: string) => {
        switch (status) {
            case "active":
                return <CheckCircle className="size-4 text-green-500" />
            case "expired":
            case "inactive":
                return <XCircle className="size-4 text-red-500" />
            case "in_gym":
                return <Activity className="size-4 text-orange-500" />
            case "left":
                return <CheckCircle className="size-4 text-gray-500" />
            case "attended":
                return <CheckCircle className="size-4 text-green-500" />
            case "missed":
                return <XCircle className="size-4 text-red-500" />
            case "assigned":
                return <AlertCircle className="size-4 text-amber-500" />
            default:
                return <AlertCircle className="size-4 text-gray-500" />
        }
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 p-6">
                {/* Welcome Section */}
                <div className="flex items-center gap-4 mb-2">
                    <div className="rounded-full">
                        <img
                            src={`/storage/${data.user.profile_image}`} // ← change to your image path
                            alt="User"
                            className="size-30 rounded-full object-cover"
                        />
                    </div>

                    <div>
                        <h1 className="text-2xl font-bold text-neutral-900 dark:text-white">Selamat Datang, {data.user.name}!</h1>
                        <p className="text-neutral-600 dark:text-neutral-400">Berikut adalah ringkasan aktivitas gym Anda</p>
                        {data.user.membership_end_date && (
                            <p className="text-sm mt-1 flex items-center gap-1.5">
                                <Calendar className="size-3.5 text-red-500" />
                                <span className="text-red-600 dark:text-red-400 font-medium">
                  Membership berakhir: {formatDate(data.user.membership_end_date)}
                </span>
                            </p>
                        )}
                    </div>
                </div>

                {/* Summary Cards */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {summaryCards.map((card, index) => (
                        <div
                            key={index}
                            className="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-6 shadow-sm hover:shadow-md transition-shadow"
                        >
                            <div className="flex items-center gap-4">
                                <div className={`rounded-full p-3 ${card.bgColor}`}>{card.icon}</div>
                                <div className="flex-1">
                                    <div className="text-sm font-medium text-neutral-600 dark:text-neutral-300">{card.title}</div>
                                    <div className="text-2xl font-bold text-neutral-900 dark:text-white">{card.value}</div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                {/* Current Membership Status */}
                {summary.currentMembership && summary.currentMembershipPackage && (
                    <div className="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20 p-6">
                        <div className="flex items-center gap-4">
                            <div className="rounded-full bg-red-100 dark:bg-red-900/30 p-3">
                                <CreditCard className="size-6 text-red-600 dark:text-red-400" />
                            </div>
                            <div className="flex-1">
                                <h3 className="text-lg font-semibold text-neutral-900 dark:text-white">Membership Aktif</h3>
                                <p className="text-neutral-600 dark:text-neutral-300">{summary.currentMembershipPackage.name}</p>
                                <p className="text-sm text-neutral-500 dark:text-neutral-400">
                                    Berlaku hingga: {formatDate(summary.currentMembership.end_date)}
                                </p>
                            </div>
                            <div className="flex items-center gap-2">
                                {getStatusIcon(summary.currentMembership.status)}
                                <span className="text-sm font-medium capitalize text-neutral-700 dark:text-neutral-300">
                  {summary.currentMembership.status}
                </span>
                            </div>
                        </div>
                    </div>
                )}

                {/* Main Content Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Recent Gym Visits */}
                    <div className="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-6">
                        <div className="flex items-center gap-3 mb-4">
                            <MapPin className="size-5 text-red-600 dark:text-red-400" />
                            <h3 className="text-lg font-semibold text-neutral-900 dark:text-white">Kunjungan Terakhir</h3>
                        </div>
                        <div className="space-y-3 max-h-80 overflow-y-auto">
                            {data.gymVisits.slice(0, 5).map((visit) => (
                                <div
                                    key={visit.id}
                                    className="flex items-center justify-between p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800/50"
                                >
                                    <div className="flex items-center gap-3">
                                        <div className="rounded-full bg-red-100 dark:bg-red-900/30 p-2">
                                            <Clock className="size-4 text-red-600 dark:text-red-400" />
                                        </div>
                                        <div>
                                            <div className="font-medium text-neutral-900 dark:text-white">{formatDate(visit.visit_date)}</div>
                                            <div className="text-sm text-neutral-600 dark:text-neutral-400">
                                                Masuk: {formatTime(visit.entry_time)}
                                                {visit.exit_time && ` • Keluar: ${formatTime(visit.exit_time)}`}
                                            </div>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        {getStatusIcon(visit.status)}
                                        <span className="text-xs font-medium capitalize text-neutral-600 dark:text-neutral-400">
                      {visit.status === "in_gym" ? "Di Gym" : "Selesai"}
                    </span>
                                    </div>
                                </div>
                            ))}
                            {data.gymVisits.length === 0 && (
                                <div className="text-center py-8 text-neutral-500 dark:text-neutral-400">Belum ada kunjungan gym</div>
                            )}
                        </div>
                    </div>

                    {/* Recent Gym Classes */}
                    <div className="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-6">
                        <div className="flex items-center gap-3 mb-4">
                            <Dumbbell className="size-5 text-amber-600 dark:text-amber-400" />
                            <h3 className="text-lg font-semibold text-neutral-900 dark:text-white">Kelas Gym Terakhir</h3>
                        </div>
                        <div className="space-y-3 max-h-80 overflow-y-auto">
                            {data.gymClassAttendances.slice(0, 5).map((attendance) => (
                                <div
                                    key={attendance.id}
                                    className="flex items-center justify-between p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800/50"
                                >
                                    <div className="flex items-center gap-3">
                                        <div className="rounded-full bg-amber-100 dark:bg-amber-900/30 p-2">
                                            <Calendar className="size-4 text-amber-600 dark:text-amber-400" />
                                        </div>
                                        <div>
                                            <div className="font-medium text-neutral-900 dark:text-white">
                                                {attendance.gym_class_schedule.gym_class.name}
                                            </div>
                                            <div className="text-sm text-neutral-600 dark:text-neutral-400">
                                                {attendance.gym_class_schedule.date ? formatDate(attendance.gym_class_schedule.date) : "Belum hadir"}
                                            </div>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        {getStatusIcon(attendance.status)}
                                        <span className="text-xs font-medium capitalize text-neutral-600 dark:text-neutral-400">
                      {attendance.status === "attended"
                          ? "Hadir"
                          : attendance.status === "missed"
                              ? "Tidak Hadir"
                              : "Terjadwal"}
                    </span>
                                    </div>
                                </div>
                            ))}
                            {data.gymClassAttendances.length === 0 && (
                                <div className="text-center py-8 text-neutral-500 dark:text-neutral-400">
                                    Belum ada kelas gym yang diikuti
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Membership History */}
                <div className="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-6">
                    <div className="flex items-center gap-3 mb-4">
                        <Flame className="size-5 text-orange-600 dark:text-orange-400" />
                        <h3 className="text-lg font-semibold text-neutral-900 dark:text-white">Riwayat Membership</h3>
                    </div>
                    <div className="overflow-x-auto">
                        <div className="space-y-3">
                            {data.membershipHistories.map((membership) => (
                                <div
                                    key={membership.id}
                                    className="flex items-center justify-between p-4 rounded-lg bg-neutral-50 dark:bg-neutral-800/50"
                                >
                                    <div className="flex items-center gap-4">
                                        <div className="rounded-full bg-orange-100 dark:bg-orange-900/30 p-2">
                                            <CreditCard className="size-4 text-orange-600 dark:text-orange-400" />
                                        </div>
                                        <div>
                                            <div className="font-medium text-neutral-900 dark:text-white">
                                                {membership.membership_package.name || `Membership #${membership.id}`}
                                            </div>
                                            <div className="text-sm text-neutral-600 dark:text-neutral-400">
                                                {formatDate(membership.start_date)} - {formatDate(membership.end_date)}
                                            </div>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        {getStatusIcon(membership.status)}
                                        <span className="text-sm font-medium capitalize text-neutral-600 dark:text-neutral-400">
                      {membership.status === "active" ? "Aktif" : "Berakhir"}
                    </span>
                                    </div>
                                </div>
                            ))}
                            {data.membershipHistories.length === 0 && (
                                <div className="text-center py-8 text-neutral-500 dark:text-neutral-400">
                                    Belum ada riwayat membership
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    )
}

