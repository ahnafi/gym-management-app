import { Head } from "@inertiajs/react"
import AppLayout from "@/layouts/app-layout"
import type { BreadcrumbItem } from "@/types"
import { Link } from "@inertiajs/react"
import type { PersonalTrainer } from "@/types"
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
    Zap,
    Heart,
    CheckCircle,
} from "lucide-react"

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Personal Trainer",
        href: "/personal-trainers",
    },
]

export default function PersonalTrainers({ trainers }: { trainers: PersonalTrainer[] }) {
    const getTrainerIcon = (index: number) => {
        const icons = [Trophy, Target, Zap, Heart, Award, Star]
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

    const specializations = [
        "Strength Training",
        "Weight Loss",
        "Muscle Building",
        "Cardio Expert",
        "Flexibility",
        "Nutrition",
    ]

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Personal Trainer" />

            {/* Hero Section */}
            <div className="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20 py-12">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <div className="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 mb-4">
                            <Users className="size-4 mr-2" />
                            Tim Pelatih Profesional
                        </div>
                        <h1 className="text-4xl font-extrabold text-gray-500 dark:text-white sm:text-5xl md:text-6xl">
                            <span className="block">Personal Trainer</span>
                            <span className="block text-red-600 dark:text-red-400">Terbaik untuk Anda</span>
                        </h1>
                        <p className="mt-3 max-w-md mx-auto text-base text-gray-500 dark:text-gray-400 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                            Raih target fitness Anda dengan bimbingan pelatih profesional berpengalaman. Dapatkan program latihan yang
                            disesuaikan dengan kebutuhan dan tujuan Anda.
                        </p>
                        <div className="mt-8 flex justify-center space-x-4">
                            <div className="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                <CheckCircle className="size-4 text-green-500 mr-2" />
                                Program Personal
                            </div>
                            <div className="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                <CheckCircle className="size-4 text-green-500 mr-2" />
                                Pelatih Bersertifikat
                            </div>
                            <div className="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                <CheckCircle className="size-4 text-green-500 mr-2" />
                                Hasil Terjamin
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
                            <div className="text-3xl font-bold text-red-600 dark:text-red-400">{trainers.length}+</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Pelatih Profesional</div>
                        </div>
                        <div className="text-center">
                            <div className="text-3xl font-bold text-orange-600 dark:text-orange-400">500+</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Member Puas</div>
                        </div>
                        <div className="text-center">
                            <div className="text-3xl font-bold text-yellow-600 dark:text-yellow-400">5+</div>
                            <div className="text-sm text-gray-600 dark:text-gray-400">Tahun Pengalaman</div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Trainers Grid */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                {trainers.length > 0 ? (
                    <>
                        <div className="text-center mb-12">
                            <h2 className="text-3xl font-bold text-gray-500 dark:text-white">Pilih Pelatih Terbaik Anda</h2>
                            <p className="mt-4 text-lg text-gray-600 dark:text-gray-300">
                                Setiap pelatih memiliki keahlian khusus untuk membantu Anda mencapai target fitness
                            </p>
                        </div>

                        <div className="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                            {trainers.map((trainer, index) => (
                                <div
                                    key={trainer.id}
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
                                        <div className="relative h-64 overflow-hidden">
                                            {trainer.images && trainer.images.length > 0 ? (
                                                <img
                                                    src={`/storage/${trainer.images[0]}`}
                                                    alt={trainer.nickname}
                                                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                                />
                                            ) : (
                                                <div
                                                    className={`w-full h-full bg-gradient-to-br ${getGradientClass(index)} flex items-center justify-center`}
                                                >
                                                    <User className="size-16 text-white/50" />
                                                </div>
                                            )}

                                            {/* Overlay Gradient */}
                                            <div className="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent" />

                                            {/* Trainer Badge */}
                                            <div
                                                className={`absolute top-4 right-4 p-2 rounded-full bg-gradient-to-r ${getGradientClass(index)} text-white shadow-lg`}
                                            >
                                                {getTrainerIcon(index)}
                                            </div>

                                            {/* Rating Badge */}
                                            <div className="absolute top-4 left-4 flex items-center px-2 py-1 bg-white/90 dark:bg-zinc-900/90 rounded-full text-xs font-medium">
                                                <Star className="size-3 text-yellow-400 mr-1" />
                                                <span className="text-gray-500 dark:text-white">4.9</span>
                                            </div>
                                        </div>

                                        {/* Content Section */}
                                        <div className="p-6 space-y-4">
                                            {/* Header */}
                                            <div className="space-y-2">
                                                <div className="flex items-center justify-between">
                                                    <h3 className="text-xl font-bold text-gray-500 dark:text-white group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-red-600 group-hover:to-orange-600 transition-all duration-300">
                                                        Coach {trainer.nickname}
                                                    </h3>
                                                    {trainer.code && (
                                                        <span className="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-full">
                              {trainer.code}
                            </span>
                                                    )}
                                                </div>

                                                <p className="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                                    {trainer.description ||
                                                        "Pelatih profesional dengan pengalaman bertahun-tahun dalam membantu member mencapai target fitness mereka."}
                                                </p>
                                            </div>

                                            {/* Specializations */}
                                            <div className="space-y-2">
                                                <h4 className="text-sm font-medium text-gray-500 dark:text-white">Spesialisasi:</h4>
                                                <div className="flex flex-wrap gap-1">
                                                    {specializations.slice(0, 3).map((spec, specIndex) => (
                                                        <span
                                                            key={specIndex}
                                                            className="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-md"
                                                        >
                              {spec}
                            </span>
                                                    ))}
                                                </div>
                                            </div>

                                            {/* Features */}
                                            <div className="space-y-2">
                                                <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                                    <Calendar className="size-4 text-red-600" />
                                                    <span>Jadwal Fleksibel</span>
                                                </div>
                                                <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                                    <Dumbbell className="size-4 text-orange-600" />
                                                    <span>Program Personal</span>
                                                </div>
                                                <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                                    <Award className="size-4 text-yellow-600" />
                                                    <span>Bersertifikat</span>
                                                </div>
                                            </div>

                                            {/* CTA Button */}
                                            <Link
                                                href={route("personal-trainers.package", trainer.slug)}
                                                className={`w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r ${getGradientClass(index)} text-white rounded-xl font-medium hover:shadow-lg transform hover:scale-105 transition-all duration-300 group-hover:shadow-xl`}
                                            >
                                                <span>Lihat Paket Latihan</span>
                                                <ArrowRight className="size-4 group-hover:translate-x-1 transition-transform" />
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>

                        {/* Call to Action Section */}
                        <div className="mt-16 bg-gradient-to-r from-red-600 to-orange-600 rounded-2xl p-8 text-center text-white">
                            <h2 className="text-2xl font-bold mb-4">Belum Menemukan Pelatih yang Cocok?</h2>
                            <p className="text-red-100 mb-6 max-w-2xl mx-auto">
                                Tim customer service kami siap membantu Anda menemukan pelatih yang sesuai dengan kebutuhan dan target
                                fitness Anda.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <button className="px-6 py-3 bg-white text-red-600 rounded-xl font-medium hover:bg-gray-100 transition-colors">
                                    Konsultasi Gratis
                                </button>
                                <button className="px-6 py-3 border-2 border-white text-white rounded-xl font-medium hover:bg-white hover:text-red-600 transition-colors">
                                    Hubungi Kami
                                </button>
                            </div>
                        </div>
                    </>
                ) : (
                    /* Empty State */
                    <div className="text-center py-16">
                        <div className="mx-auto w-24 h-24 bg-gradient-to-r from-red-100 to-orange-100 dark:from-red-900/30 dark:to-orange-900/30 rounded-full flex items-center justify-center mb-6">
                            <Users className="size-12 text-red-600 dark:text-red-400" />
                        </div>
                        <h3 className="text-2xl font-bold text-gray-500 dark:text-white mb-4">Personal Trainer Segera Hadir</h3>
                        <p className="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                            Kami sedang mempersiapkan tim pelatih profesional terbaik untuk membantu perjalanan fitness Anda.
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <button className="px-6 py-3 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition-colors">
                                Daftar Notifikasi
                            </button>
                            <button className="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                Kembali ke Beranda
                            </button>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    )
}
