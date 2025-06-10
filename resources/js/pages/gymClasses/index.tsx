import AppLayout from "@/layouts/app-layout"
import type { BreadcrumbItem } from "@/types"
import type { GymClass } from "@/types"
import { Head } from "@inertiajs/react"
import { Link } from "@inertiajs/react"
import {
    Calendar,
    Clock,
    Users,
    Star,
    ArrowRight,
    Dumbbell,
    Zap,
    Heart,
    Trophy,
    Target,
    CheckCircle,
    CreditCard,
} from "lucide-react"

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Kelas Gym",
        href: "/gym-classes",
    },
]

export default function GymClasses({ gymClasses }: { gymClasses: GymClass[] }) {
    const getClassIcon = (index: number) => {
        const icons = [Dumbbell, Zap, Heart, Trophy, Target, Star]
        const Icon = icons[index % icons.length]
        return <Icon className="size-5" />
    }

    const getGradientClass = (index: number) => {
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

    const formatPrice = (price: number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(price)
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kelas Gym" />

            {/* Hero Section */}
            <div className="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20 py-12">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <div className="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 mb-4">
                            <Users className="size-4 mr-2" />
                            Kelas Fitness Profesional
                        </div>
                        <h1 className="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                            <span className="block">Kelas Gym</span>
                            <span className="block text-red-600 dark:text-red-400">Terbaik untuk Anda</span>
                        </h1>
                        <p className="mt-3 max-w-md mx-auto text-base text-gray-500 dark:text-gray-400 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                            Bergabunglah dengan kelas-kelas fitness yang dipimpin oleh instruktur berpengalaman. Temukan kelas yang
                            sesuai dengan level dan minat Anda.
                        </p>
                        <div className="mt-8 flex justify-center space-x-4">
                            <div className="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                <CheckCircle className="size-4 text-green-500 mr-2" />
                                Instruktur Bersertifikat
                            </div>
                            <div className="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                <CheckCircle className="size-4 text-green-500 mr-2" />
                                Jadwal Fleksibel
                            </div>
                            <div className="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                <CheckCircle className="size-4 text-green-500 mr-2" />
                                Semua Level
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Stats Section */}
            <div className="bg-white dark:bg-zinc-900 py-8 border-b border-gray-200 dark:border-gray-800">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div className="text-center">
                            <div className="text-3xl font-bold text-red-600 dark:text-red-400">{gymClasses.length}+</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Kelas Tersedia</div>
                        </div>
                        <div className="text-center">
                            <div className="text-3xl font-bold text-orange-600 dark:text-orange-400">200+</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Member Aktif</div>
                        </div>
                        <div className="text-center">
                            <div className="text-3xl font-bold text-yellow-600 dark:text-yellow-400">4.8</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Rating Rata-rata</div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Classes Grid */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                {gymClasses.length > 0 ? (
                    <>
                        <div className="text-center mb-12">
                            <h2 className="text-3xl font-bold text-gray-900 dark:text-white">Pilih Kelas Favorit Anda</h2>
                            <p className="mt-4 text-lg text-gray-600 dark:text-gray-300">
                                Dari yoga hingga HIIT, temukan kelas yang cocok dengan gaya hidup Anda
                            </p>
                        </div>

                        <div className="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                            {gymClasses.map((kelas, index) => (
                                <Link
                                    key={kelas.id}
                                    href={route("gym-classes.detail", { gymClass: kelas.slug })}
                                    className="group relative bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-transparent hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2"
                                >
                                    {/* Gradient Border Effect */}
                                    <div
                                        className={`absolute inset-0 bg-gradient-to-r ${getGradientClass(index)} opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl`}
                                    />
                                    <div className="absolute inset-[1px] bg-white dark:bg-zinc-900 rounded-2xl" />

                                    {/* Content Container */}
                                    <div className="relative z-10">
                                        {/* Image Section */}
                                        <div className="relative h-48 overflow-hidden">
                                            {kelas.images && kelas.images.length > 0 ? (
                                                <img
                                                    src={`/storage/${kelas.images[0]}`}
                                                    alt={kelas.name}
                                                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                                />
                                            ) : (
                                                <div
                                                    className={`w-full h-full bg-gradient-to-br ${getGradientClass(index)} flex items-center justify-center`}
                                                >
                                                    <div className="text-white">{getClassIcon(index)}</div>
                                                </div>
                                            )}

                                            {/* Overlay Gradient */}
                                            <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent" />

                                            {/* Class Icon */}
                                            <div
                                                className={`absolute top-4 right-4 p-2 rounded-full bg-gradient-to-r ${getGradientClass(index)} text-white shadow-lg`}
                                            >
                                                {getClassIcon(index)}
                                            </div>

                                            {/* Rating Badge */}
                                            <div className="absolute top-4 left-4 flex items-center px-2 py-1 bg-white/90 dark:bg-zinc-900/90 rounded-full text-xs font-medium">
                                                <Star className="size-3 text-yellow-400 mr-1" />
                                                <span className="text-gray-900 dark:text-white">4.8</span>
                                            </div>
                                        </div>

                                        {/* Content Section */}
                                        <div className="p-6 space-y-4">
                                            {/* Header */}
                                            <div className="space-y-2">
                                                <div className="flex items-center justify-between">
                                                    <h2 className="text-xl font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-red-600 group-hover:to-orange-600 transition-all duration-300">
                                                        {kelas.name}
                                                    </h2>
                                                    {kelas.code && (
                                                        <span className="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-full">
                              {kelas.code}
                            </span>
                                                    )}
                                                </div>

                                                <p className="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                                    {kelas.description ||
                                                        "Kelas fitness yang dirancang untuk membantu Anda mencapai target kesehatan dan kebugaran."}
                                                </p>
                                            </div>

                                            {/* Features */}
                                            <div className="space-y-3">
                                                <div className="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                                                    <div
                                                        className={`p-1.5 rounded-full bg-gradient-to-r ${getGradientClass(index)} bg-opacity-10`}
                                                    >
                                                        <Clock className="size-4 text-gray-700 dark:text-gray-300" />
                                                    </div>
                                                    <span>60 menit per sesi</span>
                                                </div>

                                                <div className="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                                                    <div
                                                        className={`p-1.5 rounded-full bg-gradient-to-r ${getGradientClass(index)} bg-opacity-10`}
                                                    >
                                                        <Users className="size-4 text-red-600" />
                                                    </div>
                                                    <span>Maksimal 15 peserta</span>
                                                </div>

                                                <div className="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                                                    <div
                                                        className={`p-1.5 rounded-full bg-gradient-to-r ${getGradientClass(index)} bg-opacity-10`}
                                                    >
                                                        <Calendar className="size-4 text-orange-600" />
                                                    </div>
                                                    <span>Jadwal fleksibel</span>
                                                </div>
                                            </div>

                                            {/* Pricing */}
                                            <div className="pt-4 border-t border-gray-100 dark:border-gray-800">
                                                <div className="flex items-center justify-between">
                                                    <div>
                                                        <div className="text-2xl font-bold text-gray-900 dark:text-white bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">
                                                            {formatPrice(kelas.price)}
                                                        </div>
                                                        <div className="text-sm text-gray-500 dark:text-gray-400">per sesi</div>
                                                    </div>

                                                    {/* CTA Arrow */}
                                                    <div
                                                        className={`p-2 rounded-full bg-gradient-to-r ${getGradientClass(index)} text-white group-hover:scale-110 transition-transform duration-300 shadow-lg`}
                                                    >
                                                        <ArrowRight className="size-4" />
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Popular Badge */}
                                            {index < 3 && (
                                                <div className="absolute top-4 left-4 px-3 py-1 bg-gradient-to-r from-red-500 to-orange-500 text-white text-xs font-bold rounded-full shadow-lg">
                                                    <div className="flex items-center gap-1">
                                                        <Star className="size-3" />
                                                        POPULER
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </Link>
                            ))}
                        </div>

                        {/* Call to Action Section */}
                        <div className="mt-16 bg-gradient-to-r from-red-600 to-orange-600 rounded-2xl p-8 text-center text-white">
                            <h2 className="text-2xl font-bold mb-4">Siap Memulai Perjalanan Fitness Anda?</h2>
                            <p className="text-red-100 mb-6 max-w-2xl mx-auto">
                                Bergabunglah dengan ribuan member yang telah merasakan manfaat luar biasa dari kelas-kelas fitness kami.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <button className="px-6 py-3 bg-white text-red-600 rounded-xl font-medium hover:bg-gray-100 transition-colors flex items-center justify-center gap-2">
                                    <CreditCard className="size-4" />
                                    Daftar Sekarang
                                </button>
                                <button className="px-6 py-3 border-2 border-white text-white rounded-xl font-medium hover:bg-white hover:text-red-600 transition-colors">
                                    Lihat Jadwal
                                </button>
                            </div>
                        </div>
                    </>
                ) : (
                    /* Empty State */
                    <div className="text-center py-16">
                        <div className="mx-auto w-24 h-24 bg-gradient-to-r from-red-100 to-orange-100 dark:from-red-900/30 dark:to-orange-900/30 rounded-full flex items-center justify-center mb-6">
                            <Dumbbell className="size-12 text-red-600 dark:text-red-400" />
                        </div>
                        <h3 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">Kelas Gym Segera Hadir</h3>
                        <p className="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                            Kami sedang mempersiapkan kelas-kelas fitness terbaik untuk Anda. Pantau terus untuk update terbaru!
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <button className="px-6 py-3 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition-colors">
                                Daftar Notifikasi
                            </button>
                            <button className="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                                Kembali ke Beranda
                            </button>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    )
}
