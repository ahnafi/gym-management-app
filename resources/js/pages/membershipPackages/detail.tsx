"use client"

import { useState, useEffect } from "react"
import AppLayout from "@/layouts/app-layout"
import type { BreadcrumbItem } from "@/types"
import type { MembershipPackageCatalog } from "@/types"
import { Head } from "@inertiajs/react"
import { router } from "@inertiajs/react"
import { usePage } from "@inertiajs/react"
import { toast } from "react-toastify"
import { Button } from "@/components/ui/button"
import {
    AlertDialog,
    AlertDialogTrigger,
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
    CheckCircle,
    Clock,
    CreditCard,
    Dumbbell,
    ShieldCheck,
    Star,
    Zap,
    ArrowRight,
    ShoppingCart,
} from "lucide-react"

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Paket Membership",
        href: "/membership-packages",
    },
    {
        title: "Detail Paket",
        href: "/membership-packages/detail",
    },
]

export default function MembershipPackageDetail({ mPackage }: { mPackage: MembershipPackageCatalog }) {
    const [openDialog, setOpenDialog] = useState(false)
    const { errors } = usePage().props as unknown as { errors: Record<string, string[]> }
    const [alertMessage, setAlertMessage] = useState<string | null>(null)

    useEffect(() => {
        if (errors && Object.keys(errors).length > 0) {
            Object.values(errors).forEach((messages) => {
                messages.forEach((message) => {
                    toast.error(message, {
                        position: "top-center",
                        autoClose: 5000,
                        hideProgressBar: false,
                        closeOnClick: true,
                        pauseOnHover: true,
                        draggable: false,
                        progress: undefined,
                    })
                })
            })
        }
    }, [errors])

    const handleCheckout = () => {
        router.post("/payments/checkout", {
            purchasable_type: "membership_package",
            purchasable_id: mPackage.id,
        })
    }

    const formatPrice = (price: number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(price)
    }

    const getDurationText = (duration: number, months: number) => {
        if (months >= 12) {
            const years = Math.floor(months / 12)
            const remainingMonths = months % 12
            return years === 1
                ? remainingMonths > 0
                    ? `${years} tahun ${remainingMonths} bulan`
                    : "1 tahun"
                : remainingMonths > 0
                    ? `${years} tahun ${remainingMonths} bulan`
                    : `${years} tahun`
        }
        return `${months} bulan`
    }

    // Features based on package duration
    const packageFeatures = [
        {
            icon: <Dumbbell className="size-5 text-red-600" />,
            title: "Akses Penuh Fasilitas Gym",
            description: "Akses ke semua peralatan dan area latihan",
        },
        {
            icon: <Clock className="size-5 text-orange-600" />,
            title: "Jam Operasional 24/7",
            description: "Akses gym kapan saja sesuai kebutuhan Anda",
        },
        {
            icon: <ShieldCheck className="size-5 text-yellow-600" />,
            title: "Garansi Kepuasan",
            description: "Jaminan kualitas layanan terbaik",
        },
    ]

    // Add more features for longer packages
    if (mPackage.duration_in_months >= 3) {
        packageFeatures.push({
            icon: <Star className="size-5 text-red-500" />,
            title: "Konsultasi Fitness",
            description: "Konsultasi dengan trainer profesional",
        })
    }

    if (mPackage.duration_in_months >= 6) {
        packageFeatures.push({
            icon: <Zap className="size-5 text-orange-500" />,
            title: "Akses Kelas Premium",
            description: "Akses ke kelas-kelas eksklusif",
        })
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Paket Membership - ${mPackage.name}`} />

            {/* Hero Section */}
            <div className="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20 py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex flex-col items-center justify-center text-center">
                        <div className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 mb-4">
                            <CreditCard className="size-4 mr-2" />
                            Paket Membership
                        </div>
                        <h1 className="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                            <span className="block">{mPackage.name}</span>
                        </h1>
                        {mPackage.code && (
                            <p className="mt-3 text-base text-gray-500 dark:text-gray-400 sm:mt-5 sm:text-lg">
                                Kode: <span className="font-mono">{mPackage.code}</span>
                            </p>
                        )}
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div className="lg:grid lg:grid-cols-2 lg:gap-x-12 lg:gap-y-8">
                    {/* Left: Package Image */}
                    <div className="lg:col-span-1">
                        <div className="aspect-w-3 aspect-h-2 rounded-2xl overflow-hidden bg-gray-100 dark:bg-zinc-800 shadow-lg">
                            {mPackage.images && mPackage.images.length > 0 ? (
                                <img
                                    src={`/storage/${mPackage.images[0]}`}
                                    alt={`${mPackage.name}`}
                                    className="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500"
                                />
                            ) : (
                                <div className="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-500 to-orange-600">
                                    <Dumbbell className="size-24 text-white/50" />
                                </div>
                            )}
                        </div>

                        {/* Image Gallery (if multiple images) */}
                        {mPackage.images && mPackage.images.length > 1 && (
                            <div className="mt-4 grid grid-cols-4 gap-2">
                                {mPackage.images.slice(1, 5).map((image, index) => (
                                    <div key={index} className="aspect-w-1 aspect-h-1 rounded-md overflow-hidden">
                                        <img
                                            src={`/storage/${image}`}
                                            alt={`${mPackage.name} - ${index + 2}`}
                                            className="w-full h-full object-cover hover:opacity-80 transition-opacity cursor-pointer"
                                        />
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Right: Package Details */}
                    <div className="mt-10 lg:mt-0 lg:col-span-1">
                        <div className="space-y-8">
                            {/* Description */}
                            <div>
                                <h2 className="text-2xl font-bold text-gray-900 dark:text-white">Deskripsi Paket</h2>
                                <div className="mt-4 prose prose-indigo dark:prose-invert">
                                    <p className="text-gray-600 dark:text-gray-300">{mPackage.description}</p>
                                </div>
                            </div>

                            {/* Duration & Price */}
                            <div className="bg-white dark:bg-zinc-800 rounded-xl p-6 shadow-md border border-gray-100 dark:border-gray-700">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center">
                                        <Calendar className="size-5 text-red-600 mr-2" />
                                        <span className="text-gray-700 dark:text-gray-300">Durasi</span>
                                    </div>
                                    <span className="text-lg font-medium text-gray-900 dark:text-white">
                    {getDurationText(mPackage.duration, mPackage.duration_in_months)}
                  </span>
                                </div>
                                <div className="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <div className="flex items-center justify-between">
                                        <span className="text-gray-700 dark:text-gray-300">Harga Total</span>
                                        <span className="text-3xl font-bold text-gray-900 dark:text-white bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">
                      {formatPrice(mPackage.price)}
                    </span>
                                    </div>
                                    <div className="mt-1 text-right">
                    <span className="text-sm text-gray-500 dark:text-gray-400">
                      {mPackage.duration_in_months > 1
                          ? `${formatPrice(Math.round(mPackage.price / mPackage.duration_in_months))} per bulan`
                          : "Pembayaran satu kali"}
                    </span>
                                    </div>
                                </div>
                            </div>

                            {/* Features */}
                            <div>
                                <h2 className="text-xl font-bold text-gray-900 dark:text-white mb-4">Fitur Paket</h2>
                                <div className="space-y-4">
                                    {packageFeatures.map((feature, index) => (
                                        <div
                                            key={index}
                                            className="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-zinc-800/50 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors"
                                        >
                                            <div className="flex-shrink-0 p-1.5 rounded-full bg-white dark:bg-gray-700 shadow-sm">
                                                {feature.icon}
                                            </div>
                                            <div className="ml-4">
                                                <h3 className="text-base font-medium text-gray-900 dark:text-white">{feature.title}</h3>
                                                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">{feature.description}</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Purchase Button */}
                            <div className="mt-8">
                                <AlertDialog open={openDialog} onOpenChange={setOpenDialog}>
                                    <AlertDialogTrigger asChild>
                                        <Button
                                            size="lg"
                                            className="w-full bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white py-3 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 text-lg font-medium flex items-center justify-center gap-2"
                                        >
                                            <ShoppingCart className="size-5" />
                                            Beli Sekarang
                                            <ArrowRight className="size-5 ml-1" />
                                        </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent className="bg-white dark:bg-zinc-900 rounded-xl shadow-2xl border-0 max-w-md">
                                        <AlertDialogHeader>
                                            <AlertDialogTitle className="text-2xl font-bold text-gray-900 dark:text-white">
                                                Konfirmasi Pembelian
                                            </AlertDialogTitle>
                                            <AlertDialogDescription className="text-base text-gray-600 dark:text-gray-300">
                                                Apakah Anda yakin ingin membeli Paket Membership{" "}
                                                <span className="font-semibold text-red-600 dark:text-red-400">{mPackage.name}</span> seharga{" "}
                                                <span className="font-semibold text-red-600 dark:text-red-400">
                          {formatPrice(mPackage.price)}
                        </span>{" "}
                                                untuk {getDurationText(mPackage.duration, mPackage.duration_in_months)}?
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter className="gap-3">
                                            <AlertDialogCancel className="bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-zinc-800 dark:hover:bg-gray-700 dark:text-gray-200 border-0">
                                                Batal
                                            </AlertDialogCancel>
                                            <AlertDialogAction
                                                onClick={handleCheckout}
                                                className="bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white border-0"
                                            >
                                                Konfirmasi Pembelian
                                            </AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            </div>

                            {/* Value Badge */}
                            {mPackage.duration_in_months >= 6 && (
                                <div className="mt-6 flex items-center justify-center">
                                    <div className="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-red-100 to-orange-100 dark:from-red-900/30 dark:to-orange-900/30 text-red-800 dark:text-red-300 text-sm font-medium">
                                        <Star className="size-4 mr-2 text-orange-500" />
                                        Hemat hingga {Math.round((1 - mPackage.price / (mPackage.price * 1.2)) * 100)}% dibanding pembelian
                                        bulanan
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Additional Information */}
                <div className="mt-16 border-t border-gray-200 dark:border-gray-800 pt-10">
                    <div className="prose prose-indigo max-w-none dark:prose-invert">
                        <h2 className="text-2xl font-bold text-gray-900 dark:text-white">Informasi Tambahan</h2>
                        <div className="mt-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h3 className="text-lg font-medium text-gray-900 dark:text-white">Syarat dan Ketentuan</h3>
                                <ul className="mt-4 space-y-2 text-gray-600 dark:text-gray-300">
                                    <li className="flex items-start">
                                        <CheckCircle className="size-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>Merupakan member teregistrasi</span>
                                    </li>
                                    <li className="flex items-start">
                                        <CheckCircle className="size-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>Paket membership aktif segera setelah pembayaran berhasil</span>
                                    </li>
                                    <li className="flex items-start">
                                        <CheckCircle className="size-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>Masa aktif dihitung sejak tanggal aktivasi</span>
                                    </li>
                                    <li className="flex items-start">
                                        <CheckCircle className="size-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>Membership tidak dapat dipindahtangankan</span>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h3 className="text-lg font-medium text-gray-900 dark:text-white">Fasilitas Tambahan</h3>
                                <ul className="mt-4 space-y-2 text-gray-600 dark:text-gray-300">
                                    <li className="flex items-start">
                                        <CheckCircle className="size-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>Akses loker pribadi</span>
                                    </li>
                                    <li className="flex items-start">
                                        <CheckCircle className="size-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>Ruang ganti dan shower</span>
                                    </li>
                                    <li className="flex items-start">
                                        <CheckCircle className="size-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>Area parkir khusus member</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    )
}
