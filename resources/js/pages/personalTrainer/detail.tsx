"use client"

import { useState } from "react"
import AppLayout from "@/layouts/app-layout"
import type { BreadcrumbItem, PersonalTrainerDetail } from "@/types"
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
    User,
    Star,
    Award,
    Dumbbell,
    ArrowRight,
    Users,
    Calendar,
    Trophy,
    Target,
    CheckCircle,
    Clock,
    ShoppingCart,
} from "lucide-react"

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Personal Trainer",
        href: "/personal-trainers",
    },
    {
        title: "Detail",
        href: "/personal-trainers/packages",
    },
]

export default function PersonalTrainerDetails({ ptDetail }: { ptDetail: PersonalTrainerDetail }) {
    const [selectedPackage, setSelectedPackage] = useState<null | {
        id: number
        nickname: string
        name: string
        duration: number
        price: number
    }>(null)

    const handleCheckout = (ptId: number) => {
        setSelectedPackage(null)
        router.post("/payments/checkout", {
            purchasable_type: "personal_trainer_package",
            purchasable_id: ptId,
        })
    }

    const formatPrice = (price: number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(price)
    }

    const getPackageGradient = (index: number) => {
        const gradients = [
            "from-red-500 to-orange-500",
            "from-orange-500 to-yellow-500",
            "from-yellow-500 to-amber-500",
            "from-rose-500 to-red-500",
            "from-amber-500 to-orange-500",
            "from-red-600 to-rose-500",
        ]
        return gradients[index % gradients.length]
    }

    const trainerSpecializations = [
        "Strength Training",
        "Weight Loss",
        "Muscle Building",
        "Cardio Expert",
        "Flexibility Training",
        "Nutrition Guidance",
    ]

    const trainerAchievements = [
        {
            icon: <Trophy className="size-4 text-yellow-600" />,
            title: "Certified Personal Trainer",
            description: "Bersertifikat internasional",
        },
        {
            icon: <Users className="size-4 text-red-600" />,
            title: "100+ Klien Sukses",
            description: "Pengalaman melatih berbagai kalangan",
        },
        {
            icon: <Star className="size-4 text-orange-600" />,
            title: "Rating 4.9/5",
            description: "Kepuasan klien terjamin",
        },
        {
            icon: <Award className="size-4 text-amber-600" />,
            title: "5+ Tahun Pengalaman",
            description: "Berpengalaman di industri fitness",
        },
    ]

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Personal Trainer - ${ptDetail.nickname}`} />

            {/* Hero Section */}
            <div className="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20 py-12">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <div className="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 mb-4">
                            <User className="size-4 mr-2" />
                            Personal Trainer Profesional
                        </div>
                        <h1 className="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl">
                            Coach {ptDetail.nickname}
                        </h1>
                        {ptDetail.code && (
                            <p className="mt-2 text-lg text-gray-600 dark:text-gray-400">
                                ID Trainer: <span className="font-mono text-red-600 dark:text-red-400">{ptDetail.code}</span>
                            </p>
                        )}
                        <p className="mt-4 max-w-2xl mx-auto text-lg text-gray-600 dark:text-gray-300">
                            Raih target fitness Anda dengan bimbingan personal dari trainer berpengalaman
                        </p>
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                {/* Trainer Profile Section */}
                <div className="lg:grid lg:grid-cols-3 lg:gap-x-12 lg:gap-y-8 mb-16">
                    {/* Left: Trainer Image */}
                    <div className="lg:col-span-1">
                        <div className="aspect-w-3 aspect-h-4 rounded-2xl overflow-hidden bg-gray-100 dark:bg-zinc-800 shadow-lg mb-6">
                            {ptDetail.images && ptDetail.images.length > 0 ? (
                                <img
                                    src={`/storage/${ptDetail.images[0]}`}
                                    alt={ptDetail.nickname}
                                    className="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500"
                                />
                            ) : (
                                <div className="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-500 to-orange-600">
                                    <User className="size-24 text-white/50" />
                                </div>
                            )}
                        </div>

                        {/* Quick Stats */}
                        <div className="bg-white dark:bg-zinc-800 rounded-xl p-6 shadow-md border border-gray-100 dark:border-gray-700">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistik Trainer</h3>
                            <div className="space-y-4">
                                <div className="flex justify-between items-center">
                                    <span className="text-gray-600 dark:text-gray-400">Rating</span>
                                    <div className="flex items-center gap-1">
                                        <Star className="size-4 text-yellow-400 fill-current" />
                                        <span className="font-semibold text-gray-900 dark:text-white">4.9</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-gray-600 dark:text-gray-400">Total Klien</span>
                                    <span className="font-semibold text-gray-900 dark:text-white">100+</span>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-gray-600 dark:text-gray-400">Pengalaman</span>
                                    <span className="font-semibold text-gray-900 dark:text-white">5+ Tahun</span>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-gray-600 dark:text-gray-400">Paket Tersedia</span>
                                    <span className="font-semibold text-gray-900 dark:text-white">
                    {ptDetail.personalTrainerPackages.length}
                  </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right: Trainer Details */}
                    <div className="mt-10 lg:mt-0 lg:col-span-2">
                        <div className="space-y-8">
                            {/* About Section */}
                            <div className="bg-white dark:bg-zinc-800 rounded-xl p-6 shadow-md border border-gray-100 dark:border-gray-700">
                                <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                                    Tentang Coach {ptDetail.nickname}
                                </h2>
                                <p className="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                                    {ptDetail.description ||
                                        "Trainer profesional dengan dedikasi tinggi untuk membantu setiap klien mencapai target fitness mereka. Dengan pendekatan yang personal dan program latihan yang disesuaikan, saya berkomitmen untuk memberikan hasil terbaik bagi setiap klien."}
                                </p>

                                {/* Specializations */}
                                <div className="mb-6">
                                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-3">Spesialisasi</h3>
                                    <div className="flex flex-wrap gap-2">
                                        {trainerSpecializations.slice(0, 4).map((spec, index) => (
                                            <span
                                                key={index}
                                                className="px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded-full text-sm font-medium"
                                            >
                        {spec}
                      </span>
                                        ))}
                                    </div>
                                </div>

                                {/* Achievements */}
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pencapaian & Sertifikasi</h3>
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        {trainerAchievements.map((achievement, index) => (
                                            <div key={index} className="flex items-start p-3 rounded-lg bg-gray-50 dark:bg-zinc-700">
                                                <div className="flex-shrink-0 p-1.5 rounded-full bg-white dark:bg-zinc-600 shadow-sm">
                                                    {achievement.icon}
                                                </div>
                                                <div className="ml-3">
                                                    <h4 className="text-sm font-medium text-gray-900 dark:text-white">{achievement.title}</h4>
                                                    <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">{achievement.description}</p>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Training Packages Section */}
                <div>
                    <div className="text-center mb-12">
                        <h2 className="text-3xl font-bold text-gray-900 dark:text-white">Paket Latihan Personal</h2>
                        <p className="mt-4 text-lg text-gray-600 dark:text-gray-300">
                            Pilih paket yang sesuai dengan kebutuhan dan target fitness Anda
                        </p>
                    </div>

                    {ptDetail.personalTrainerPackages.length > 0 ? (
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                            {ptDetail.personalTrainerPackages.map((pkg, index) => (
                                <div
                                    key={pkg.id}
                                    className="group relative bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-transparent hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2"
                                >
                                    {/* Gradient Border Effect */}
                                    <div
                                        className={`absolute inset-0 bg-gradient-to-r ${getPackageGradient(index)} opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl`}
                                    />
                                    <div className="absolute inset-[1px] bg-white dark:bg-zinc-900 rounded-2xl" />

                                    {/* Content Container */}
                                    <div className="relative z-10">
                                        {/* Image Section */}
                                        <div className="relative h-48 overflow-hidden">
                                            {pkg.images && pkg.images.length > 0 ? (
                                                <img
                                                    src={`/storage/${pkg.images[0]}`}
                                                    alt={pkg.name}
                                                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                                />
                                            ) : (
                                                <div
                                                    className={`w-full h-full bg-gradient-to-br ${getPackageGradient(index)} flex items-center justify-center`}
                                                >
                                                    <Dumbbell className="size-16 text-white/50" />
                                                </div>
                                            )}

                                            {/* Overlay Gradient */}
                                            <div className="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent" />

                                            {/* Package Badge */}
                                            <div
                                                className={`absolute top-4 right-4 p-2 rounded-full bg-gradient-to-r ${getPackageGradient(index)} text-white shadow-lg`}
                                            >
                                                <Target className="size-4" />
                                            </div>

                                            {/* Popular Badge */}
                                            {index === 0 && (
                                                <div className="absolute top-4 left-4 px-3 py-1 bg-gradient-to-r from-red-500 to-orange-500 text-white text-xs font-bold rounded-full shadow-lg">
                                                    <div className="flex items-center gap-1">
                                                        <Star className="size-3" />
                                                        POPULER
                                                    </div>
                                                </div>
                                            )}
                                        </div>

                                        {/* Content Section */}
                                        <div className="p-6 space-y-4">
                                            {/* Header */}
                                            <div className="space-y-2">
                                                <div className="flex items-center justify-between">
                                                    <h3 className="text-xl font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-red-600 group-hover:to-orange-600 transition-all duration-300">
                                                        {pkg.name}
                                                    </h3>
                                                    {pkg.code && (
                                                        <span className="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-gray-400 rounded-full">
                              {pkg.code}
                            </span>
                                                    )}
                                                </div>

                                                <p className="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                                    {pkg.description ||
                                                        "Program latihan personal yang dirancang khusus untuk mencapai target Anda dengan efektif dan efisien."}
                                                </p>
                                            </div>

                                            {/* Package Features */}
                                            <div className="space-y-3">
                                                <div className="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                                                    <div
                                                        className={`p-1.5 rounded-full bg-gradient-to-r ${getPackageGradient(index)} bg-opacity-10`}
                                                    >
                                                        <Calendar className="size-4 text-gray-700 dark:text-gray-300" />
                                                    </div>
                                                    <span>{pkg.day_duration} sesi pertemuan</span>
                                                </div>

                                                <div className="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                                                    <div
                                                        className={`p-1.5 rounded-full bg-gradient-to-r ${getPackageGradient(index)} bg-opacity-10`}
                                                    >
                                                        <Clock className="size-4 text-orange-600" />
                                                    </div>
                                                    <span>Jadwal fleksibel</span>
                                                </div>

                                                <div className="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                                                    <div
                                                        className={`p-1.5 rounded-full bg-gradient-to-r ${getPackageGradient(index)} bg-opacity-10`}
                                                    >
                                                        <CheckCircle className="size-4 text-green-600" />
                                                    </div>
                                                    <span>Program personal</span>
                                                </div>
                                            </div>

                                            {/* Pricing */}
                                            <div className="pt-4 border-t border-gray-100 dark:border-gray-800">
                                                <div className="flex items-center justify-between mb-4">
                                                    <div>
                                                        <div className="text-2xl font-bold text-gray-900 dark:text-white">
                                                            {formatPrice(pkg.price)}
                                                        </div>
                                                        <div className="text-sm text-gray-500 dark:text-gray-400">
                                                            {formatPrice(Math.round(pkg.price / pkg.day_duration))} per sesi
                                                        </div>
                                                    </div>
                                                </div>

                                                {/* CTA Button */}
                                                <Button
                                                    className={`w-full bg-gradient-to-r ${getPackageGradient(index)} text-white hover:shadow-lg transform hover:scale-105 transition-all duration-300 group-hover:shadow-xl flex items-center justify-center gap-2`}
                                                    onClick={() =>
                                                        setSelectedPackage({
                                                            id: pkg.id,
                                                            nickname: ptDetail.nickname,
                                                            name: pkg.name,
                                                            duration: pkg.day_duration,
                                                            price: pkg.price,
                                                        })
                                                    }
                                                >
                                                    <ShoppingCart className="size-4" />
                                                    Pilih Paket Ini
                                                    <ArrowRight className="size-4 group-hover:translate-x-1 transition-transform" />
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        /* Empty State */
                        <div className="text-center py-16">
                            <div className="mx-auto w-24 h-24 bg-gradient-to-r from-red-100 to-orange-100 dark:from-red-900/30 dark:to-orange-900/30 rounded-full flex items-center justify-center mb-6">
                                <Dumbbell className="size-12 text-red-600 dark:text-red-400" />
                            </div>
                            <h3 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">Paket Segera Hadir</h3>
                            <p className="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                                Trainer ini sedang mempersiapkan paket latihan terbaik untuk Anda. Pantau terus untuk update terbaru!
                            </p>
                            <Button variant="outline" className="border-red-600 text-red-600 hover:bg-red-50">
                                Daftar Notifikasi
                            </Button>
                        </div>
                    )}
                </div>

                {/* Call to Action Section */}
                <div className="mt-16 bg-gradient-to-r from-red-600 to-orange-600 rounded-2xl p-8 text-center text-white">
                    <h2 className="text-2xl font-bold mb-4">Siap Memulai Perjalanan Fitness Anda?</h2>
                    <p className="text-red-100 mb-6 max-w-2xl mx-auto">
                        Bergabunglah dengan ratusan klien yang telah merasakan transformasi luar biasa bersama Coach{" "}
                        {ptDetail.nickname}
                    </p>
                    <div className="flex flex-col sm:flex-row gap-4 justify-center">
                        <Button className="px-6 py-3 bg-white text-red-600 rounded-xl font-medium hover:bg-gray-100 transition-colors">
                            Konsultasi Gratis
                        </Button>
                        <Button
                            variant="outline"
                            className="px-6 py-3 border-2 border-white text-white rounded-xl font-medium hover:bg-white hover:text-red-600 transition-colors"
                        >
                            Hubungi Trainer
                        </Button>
                    </div>
                </div>
            </div>

            {/* Enhanced Alert Dialog */}
            {selectedPackage && (
                <AlertDialog open={!!selectedPackage} onOpenChange={(open) => !open && setSelectedPackage(null)}>
                    <AlertDialogContent className="bg-white dark:bg-zinc-900 rounded-xl shadow-2xl border-0 max-w-md">
                        <AlertDialogHeader>
                            <div className="flex items-center gap-3 mb-2">
                                <div className="p-2 bg-red-100 dark:bg-red-900/30 rounded-full">
                                    <ShoppingCart className="size-5 text-red-600 dark:text-red-400" />
                                </div>
                                <AlertDialogTitle className="text-xl font-bold text-gray-900 dark:text-white">
                                    Konfirmasi Pembelian
                                </AlertDialogTitle>
                            </div>
                            <AlertDialogDescription className="text-base text-gray-600 dark:text-gray-300">
                                <div className="space-y-4 mt-4">
                                    <div className="bg-red-50 dark:bg-red-950/20 rounded-lg p-4">
                                        <div className="font-semibold text-red-900 dark:text-red-100">{selectedPackage.name}</div>
                                        <div className="text-sm text-red-700 dark:text-red-300">Coach {selectedPackage.nickname}</div>
                                        <div className="text-sm text-red-600 dark:text-red-400 mt-1">
                                            {selectedPackage.duration} sesi pertemuan
                                        </div>
                                    </div>
                                    <div className="flex justify-between items-center py-2 border-t border-gray-200 dark:border-gray-700">
                                        <span className="font-medium">Total Harga:</span>
                                        <span className="font-bold text-xl text-red-600 dark:text-red-400">
                      {formatPrice(selectedPackage.price)}
                    </span>
                                    </div>
                                    <div className="text-sm text-gray-500 dark:text-gray-400">
                                        Dengan melanjutkan, Anda akan mendapatkan akses ke program latihan personal yang disesuaikan dengan
                                        kebutuhan Anda.
                                    </div>
                                </div>
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter className="gap-3">
                            <AlertDialogCancel className="bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-zinc-800 dark:hover:bg-gray-700 dark:text-gray-200 border-0">
                                Batal
                            </AlertDialogCancel>
                            <AlertDialogAction
                                onClick={() => handleCheckout(selectedPackage.id)}
                                className="bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white border-0 flex items-center gap-2"
                            >
                                <CheckCircle className="size-4" />
                                Konfirmasi Pembelian
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            )}
        </AppLayout>
    )
}
