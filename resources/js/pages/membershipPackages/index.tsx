import AppLayout from "@/layouts/app-layout"
import type { BreadcrumbItem } from "@/types"
import type { MembershipPackageCatalog } from "@/types"
import { Head } from "@inertiajs/react"
import { Link } from "@inertiajs/react"
import { Calendar, CreditCard, Star, Clock, CheckCircle, ArrowRight, Sparkles, Crown, Zap } from "lucide-react"

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Paket Membership",
        href: "/membership-packages",
    },
]

export default function MembershipPackages({ packages }: { packages: MembershipPackageCatalog[] }) {
    const getPackageIcon = (index: number) => {
        const icons = [Crown, Zap, Star, Sparkles]
        const Icon = icons[index % icons.length]
        return <Icon className="size-6" />
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

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Paket Membership" />

            {/* Header Section */}
            <div className="px-6 py-8 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20">
                <div className="max-w-4xl">
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-white mb-2">Pilih Paket Membership Terbaik</h1>
                    <p className="text-lg text-gray-600 dark:text-gray-300">
                        Dapatkan akses penuh ke fasilitas gym dengan paket yang sesuai kebutuhan Anda
                    </p>
                </div>
            </div>

            {/* Packages Grid */}
            <div className="px-6 py-8">
                <div className="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 max-w-7xl mx-auto">
                    {packages.map((pkg, index) => (
                        <Link
                            href={`/membership-packages/${pkg.slug}`}
                            key={pkg.id}
                            className="group relative bg-white dark:bg-neutral-900 rounded-2xl overflow-hidden border border-gray-200 dark:border-neutral-700 hover:border-transparent hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1"
                        >
                            {/* Gradient Border Effect */}
                            <div
                                className={`absolute inset-0 bg-gradient-to-r ${getGradientClass(index)} opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl`}
                            />
                            <div className="absolute inset-[1px] bg-white dark:bg-neutral-900 rounded-2xl" />

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
                                            className={`w-full h-full bg-gradient-to-br ${getGradientClass(index)} flex items-center justify-center`}
                                        >
                                            <div className="text-white">{getPackageIcon(index)}</div>
                                        </div>
                                    )}

                                    {/* Overlay Gradient */}
                                    <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent" />

                                    {/* Package Icon */}
                                    <div
                                        className={`absolute top-4 right-4 p-2 rounded-full bg-gradient-to-r ${getGradientClass(index)} text-white shadow-lg`}
                                    >
                                        {getPackageIcon(index)}
                                    </div>
                                </div>

                                {/* Content Section */}
                                <div className="p-6 space-y-4">
                                    {/* Header */}
                                    <div className="space-y-2">
                                        <div className="flex items-center justify-between">
                                            <h2 className="text-xl font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-red-600 group-hover:to-orange-600 transition-all duration-300">
                                                {pkg.name}
                                            </h2>
                                            {pkg.code && (
                                                <span className="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-neutral-800 text-gray-600 dark:text-gray-400 rounded-full">
                          {pkg.code}
                        </span>
                                            )}
                                        </div>

                                        {pkg.description && (
                                            <p className="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{pkg.description}</p>
                                        )}
                                    </div>

                                    {/* Features */}
                                    <div className="space-y-3">
                                        <div className="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                                            <div className={`p-1.5 rounded-full bg-gradient-to-r ${getGradientClass(index)} bg-opacity-10`}>
                                                <Calendar className="size-4 text-gray-700 dark:text-gray-300" />
                                            </div>
                                            <span>Durasi: {getDurationText(pkg.duration, pkg.duration_in_months)}</span>
                                        </div>

                                        <div className="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                                            <div className={`p-1.5 rounded-full bg-gradient-to-r ${getGradientClass(index)} bg-opacity-10`}>
                                                <CheckCircle className="size-4 text-green-600" />
                                            </div>
                                            <span>Akses penuh fasilitas gym</span>
                                        </div>

                                        <div className="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                                            <div className={`p-1.5 rounded-full bg-gradient-to-r ${getGradientClass(index)} bg-opacity-10`}>
                                                <Clock className="size-4 text-blue-600" />
                                            </div>
                                            <span>24/7 akses gym</span>
                                        </div>
                                    </div>

                                    {/* Pricing */}
                                    <div className="pt-4 border-t border-gray-100 dark:border-neutral-800">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <div className="text-2xl font-bold text-gray-900 dark:text-white">{formatPrice(pkg.price)}</div>
                                                <div className="text-sm text-gray-500 dark:text-gray-400">
                                                    {pkg.duration_in_months === 1 ? "per bulan" : `untuk ${pkg.duration_in_months} bulan`}
                                                </div>
                                            </div>

                                            {/* CTA Arrow */}
                                            <div
                                                className={`p-2 rounded-full bg-gradient-to-r ${getGradientClass(index)} text-white group-hover:scale-110 transition-transform duration-300 shadow-lg`}
                                            >
                                                <ArrowRight className="size-4" />
                                            </div>
                                        </div>
                                    </div>

                                    {/* Value Badge */}
                                    {pkg.duration_in_months >= 6 && (
                                        <div className="absolute top-4 left-4 px-3 py-1 bg-gradient-to-r from-red-500 to-orange-500 text-white text-xs font-bold rounded-full shadow-lg">
                                            <div className="flex items-center gap-1">
                                                <Star className="size-3" />
                                                HEMAT
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </Link>
                    ))}
                </div>

                {/* Empty State */}
                {packages.length === 0 && (
                    <div className="text-center py-16">
                        <div className="mx-auto w-24 h-24 bg-gray-100 dark:bg-neutral-800 rounded-full flex items-center justify-center mb-4">
                            <CreditCard className="size-12 text-gray-400" />
                        </div>
                        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum Ada Paket Membership</h3>
                        <p className="text-gray-600 dark:text-gray-400">Paket membership akan segera tersedia</p>
                    </div>
                )}
            </div>
        </AppLayout>
    )
}
