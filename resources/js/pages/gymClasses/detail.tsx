"use client"

import { useState } from "react"
import AppLayout from "@/layouts/app-layout"
import type { BreadcrumbItem } from "@/types"
import type { GymClassDetail } from "@/types"
import { Head, router } from "@inertiajs/react"
import { Button } from "@/components/ui/button"
import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogFooter,
    AlertDialogTitle,
    AlertDialogDescription,
    AlertDialogCancel,
    AlertDialogAction,
} from "@/components/ui/alert-dialog"
import {
    Calendar,
    Clock,
    Users,
    Star,
    CheckCircle,
    Dumbbell,
    Heart,
    Zap,
    ArrowRight,
    CalendarCheck,
    UserCheck,
    Target,
} from "lucide-react"

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Kelas Gym", href: "/gym-classes" },
    { title: "Jadwal Kelas", href: "/gym-classes/schedule" },
]

export default function GymClassDetails({ gymClass }: { gymClass: GymClassDetail }) {
    const [selectedSchedule, setSelectedSchedule] = useState<null | {
        id: number
        name: string
        schedule: string
        price: number
    }>(null)

    const handleCheckout = (gcId: number, gcsId: number) => {
        setSelectedSchedule(null)
        router.post("/payments/checkout", {
            purchasable_type: "gym_class",
            purchasable_id: gcId,
            gym_class_schedule_id: gcsId,
        })
    }

    const formatPrice = (price: number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(price)
    }

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString("id-ID", {
            weekday: "long",
            day: "numeric",
            month: "long",
            year: "numeric",
        })
    }

    const classFeatures = [
        {
            icon: <UserCheck className="size-5 text-red-600" />,
            title: "Instruktur Berpengalaman",
            description: "Dipandu oleh trainer profesional bersertifikat",
        },
        {
            icon: <Users className="size-5 text-orange-600" />,
            title: "Kelas Grup Interaktif",
            description: "Latihan bersama dalam suasana yang menyenangkan",
        },
        {
            icon: <Target className="size-5 text-yellow-600" />,
            title: "Program Terstruktur",
            description: "Kurikulum latihan yang dirancang khusus",
        },
        {
            icon: <Heart className="size-5 text-rose-600" />,
            title: "Cocok Semua Level",
            description: "Dari pemula hingga advanced",
        },
    ]

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Kelas: ${gymClass.name}`} />

            {/* Hero Section */}
            <div className="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20 py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <div className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 mb-4">
                            <Dumbbell className="size-4 mr-2" />
                            Kelas Gym Premium
                        </div>
                        <h1 className="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl">{gymClass.name}</h1>
                        {gymClass.code && (
                            <p className="mt-2 text-lg text-gray-600 dark:text-gray-400">
                                Kode Kelas: <span className="font-mono text-red-600 dark:text-red-400">{gymClass.code}</span>
                            </p>
                        )}
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div className="lg:grid lg:grid-cols-2 lg:gap-x-12 lg:gap-y-8">
                    {/* Left: Class Image and Info */}
                    <div className="lg:col-span-1">
                        {/* Image */}
                        <div className="aspect-w-3 aspect-h-2 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 shadow-lg mb-8">
                            {gymClass.images && gymClass.images.length > 0 ? (
                                <img
                                    src={`/storage/${gymClass.images[0]}`}
                                    alt={gymClass.name}
                                    className="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500"
                                />
                            ) : (
                                <div className="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-500 to-orange-600">
                                    <Dumbbell className="size-24 text-white/50" />
                                </div>
                            )}
                        </div>

                        {/* Class Info Card */}
                        <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-100 dark:border-gray-700 mb-8">
                            <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">Tentang Kelas Ini</h2>
                            <p className="text-gray-600 dark:text-gray-300 mb-6">
                                {gymClass.description ||
                                    "Kelas fitness yang dirancang khusus untuk membantu Anda mencapai target kesehatan dan kebugaran dengan bimbingan instruktur profesional."}
                            </p>

                            {/* Price Highlight */}
                            <div className="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20 rounded-lg p-4 mb-6">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <span className="text-sm text-gray-600 dark:text-gray-400">Harga per Sesi</span>
                                        <div className="text-3xl font-bold text-gray-900 dark:text-white bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">
                                            {formatPrice(gymClass.price)}
                                        </div>
                                    </div>
                                    <div className="p-3 bg-red-100 dark:bg-red-900/30 rounded-full">
                                        <Star className="size-6 text-orange-600 dark:text-orange-400" />
                                    </div>
                                </div>
                            </div>

                            {/* Features */}
                            <div className="space-y-4">
                                <h3 className="text-lg font-semibold text-gray-900 dark:text-white">Keunggulan Kelas</h3>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {classFeatures.map((feature, index) => (
                                        <div key={index} className="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                            <div className="flex-shrink-0 p-1.5 rounded-full bg-white dark:bg-gray-600 shadow-sm">
                                                {feature.icon}
                                            </div>
                                            <div className="ml-3">
                                                <h4 className="text-sm font-medium text-gray-900 dark:text-white">{feature.title}</h4>
                                                <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">{feature.description}</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right: Schedule Section */}
                    <div className="lg:col-span-1">
                        <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-100 dark:border-gray-700 h-fit">
                            <div className="flex items-center gap-3 mb-6">
                                <div className="p-2 bg-red-100 dark:bg-red-900/30 rounded-full">
                                    <CalendarCheck className="size-5 text-red-600 dark:text-red-400" />
                                </div>
                                <div>
                                    <h2 className="text-2xl font-bold text-gray-900 dark:text-white">Pilih Jadwal Kelas</h2>
                                    <p className="text-sm text-gray-600 dark:text-gray-400">Reservasi slot Anda sekarang juga!</p>
                                </div>
                            </div>

                            {gymClass.gymClassSchedules.length > 0 ? (
                                <div className="space-y-4 max-h-96 overflow-y-auto pr-2">
                                    {gymClass.gymClassSchedules.map((schedule) => {
                                        const isSlotAvailable = schedule.available_slot > 0
                                        const slotPercentage = (schedule.available_slot / schedule.slot) * 100

                                        return (
                                            <div
                                                key={schedule.id}
                                                className={`border rounded-xl p-4 transition-all duration-300 ${
                                                    isSlotAvailable
                                                        ? "border-gray-200 dark:border-gray-700 hover:border-red-300 dark:hover:border-red-600 hover:shadow-md bg-white dark:bg-gray-900"
                                                        : "border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50"
                                                }`}
                                            >
                                                <div className="flex justify-between items-start mb-3">
                                                    <div className="flex-1">
                                                        <div className="flex items-center gap-2 mb-2">
                                                            <Calendar className="size-4 text-red-600" />
                                                            <span className="font-semibold text-gray-900 dark:text-white">
                                {formatDate(schedule.date)}
                              </span>
                                                        </div>
                                                        <div className="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                                            <div className="flex items-center gap-1">
                                                                <Clock className="size-4" />
                                                                <span>
                                  {schedule.start_time} - {schedule.end_time}
                                </span>
                                                            </div>
                                                            <div className="flex items-center gap-1">
                                                                <Users className="size-4" />
                                                                <span>
                                  {schedule.available_slot}/{schedule.slot} slot
                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {/* Slot Availability Bar */}
                                                <div className="mb-4">
                                                    <div className="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                                                        <span>Ketersediaan Slot</span>
                                                        <span>{schedule.available_slot} tersisa</span>
                                                    </div>
                                                    <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                        <div
                                                            className={`h-2 rounded-full transition-all duration-300 ${
                                                                slotPercentage > 50
                                                                    ? "bg-green-500"
                                                                    : slotPercentage > 20
                                                                        ? "bg-yellow-500"
                                                                        : slotPercentage > 0
                                                                            ? "bg-red-500"
                                                                            : "bg-gray-400"
                                                            }`}
                                                            style={{ width: `${slotPercentage}%` }}
                                                        />
                                                    </div>
                                                </div>

                                                {/* Action Button */}
                                                {isSlotAvailable ? (
                                                    <Button
                                                        className="w-full bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white py-2.5 rounded-lg font-medium flex items-center justify-center gap-2 transition-all duration-300 hover:shadow-lg"
                                                        onClick={() =>
                                                            setSelectedSchedule({
                                                                id: schedule.id,
                                                                name: gymClass.name,
                                                                schedule: `${formatDate(schedule.date)} | ${schedule.start_time} - ${schedule.end_time}`,
                                                                price: gymClass.price,
                                                            })
                                                        }
                                                    >
                                                        <CalendarCheck className="size-4" />
                                                        Reservasi Sekarang
                                                        <ArrowRight className="size-4" />
                                                    </Button>
                                                ) : (
                                                    <div className="w-full py-2.5 px-4 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-lg text-center font-medium">
                                                        Slot Penuh - Coba Jadwal Lain
                                                    </div>
                                                )}
                                            </div>
                                        )
                                    })}
                                </div>
                            ) : (
                                <div className="text-center py-12">
                                    <div className="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                        <Calendar className="size-8 text-gray-400" />
                                    </div>
                                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">Jadwal Segera Hadir</h3>
                                    <p className="text-gray-600 dark:text-gray-400 mb-4">
                                        Jadwal untuk kelas ini sedang dipersiapkan. Pantau terus untuk update terbaru!
                                    </p>
                                    <Button variant="outline" className="border-red-600 text-red-600 hover:bg-red-50">
                                        Daftar Notifikasi
                                    </Button>
                                </div>
                            )}
                        </div>

                        {/* Call to Action Card */}
                        <div className="mt-6 bg-gradient-to-r from-red-600 to-orange-600 rounded-xl p-6 text-white">
                            <div className="flex items-center gap-3 mb-4">
                                <div className="p-2 bg-white/20 rounded-full">
                                    <Zap className="size-5" />
                                </div>
                                <div>
                                    <h3 className="text-lg font-bold">Dapatkan Manfaat Maksimal!</h3>
                                    <p className="text-red-100 text-sm">
                                        Bergabunglah dengan ribuan member yang sudah merasakan hasilnya
                                    </p>
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <div className="text-2xl font-bold">500+</div>
                                    <div className="text-xs text-red-100">Member Aktif</div>
                                </div>
                                <div>
                                    <div className="text-2xl font-bold">4.9★</div>
                                    <div className="text-xs text-red-100">Rating Kelas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Enhanced Alert Dialog */}
            {selectedSchedule && (
                <AlertDialog open={!!selectedSchedule} onOpenChange={(open) => !open && setSelectedSchedule(null)}>
                    <AlertDialogContent className="bg-white dark:bg-gray-900 rounded-xl shadow-2xl border-0 max-w-md">
                        <AlertDialogHeader>
                            <div className="flex items-center gap-3 mb-2">
                                <div className="p-2 bg-red-100 dark:bg-red-900/30 rounded-full">
                                    <CalendarCheck className="size-5 text-red-600 dark:text-red-400" />
                                </div>
                                <AlertDialogTitle className="text-xl font-bold text-gray-900 dark:text-white">
                                    Konfirmasi Reservasi
                                </AlertDialogTitle>
                            </div>
                            <AlertDialogDescription className="text-base text-gray-600 dark:text-gray-300">
                                <div className="space-y-3 mt-4">
                                    <div className="bg-red-50 dark:bg-red-950/20 rounded-lg p-3">
                                        <div className="font-semibold text-red-900 dark:text-red-100">{selectedSchedule.name}</div>
                                        <div className="text-sm text-red-700 dark:text-red-300">{selectedSchedule.schedule}</div>
                                    </div>
                                    <div className="flex justify-between items-center">
                                        <span>Harga:</span>
                                        <span className="font-bold text-lg text-red-600 dark:text-red-400">
                      {formatPrice(selectedSchedule.price)}
                    </span>
                                    </div>
                                    <div className="text-sm text-gray-500 dark:text-gray-400">
                                        Dengan melanjutkan, Anda menyetujui untuk mengikuti kelas sesuai jadwal yang dipilih.
                                    </div>
                                </div>
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter className="gap-3">
                            <AlertDialogCancel className="bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200 border-0">
                                Batal
                            </AlertDialogCancel>
                            <AlertDialogAction
                                onClick={() => handleCheckout(gymClass.id, selectedSchedule.id)}
                                className="bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white border-0 flex items-center gap-2"
                            >
                                <CheckCircle className="size-4" />
                                Konfirmasi Reservasi
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            )}
        </AppLayout>
    )
}
