import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, PersonalTrainerDashboardProps } from '@/types';
import { Head } from '@inertiajs/react';
import { Calendar, CalendarDays, Users, CheckCircle, XCircle, Clock, CalendarCheck, RotateCcw, Trophy, Package, User, Timer, Award } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard Personal Trainer',
        href: '/personal-trainer-dashboard',
    },
];

export default function PersonalTrainerDashboard({summary, memberTrainee, packageSummary}: PersonalTrainerDashboardProps) {
    console.log('PersonalTrainerDashboard', { summary, memberTrainee, packageSummary });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard Personal Trainer" />

            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
                {/* Header */}
                <div className="bg-gradient-to-r from-red-50 to-orange-50 dark:from-zinc-800 dark:to-zinc-700 rounded-xl p-6 shadow-sm border border-red-100 dark:border-zinc-600">
                    <h1 className="text-2xl font-bold text-red-900 dark:text-zinc-100 mb-2">
                        Dashboard Personal Trainer
                    </h1>
                    <p className="text-red-700 dark:text-zinc-300 text-sm">
                        Pantau sesi latihan, perkembangan klien, dan performa paket latihan Anda
                    </p>
                </div>

                {/* Stats Cards */}
                <div className="grid auto-rows-min gap-6 md:grid-cols-3">
                    <div className="bg-white dark:bg-zinc-900 rounded-xl border border-red-100 dark:border-zinc-700 p-6 shadow-md hover:shadow-lg transition-shadow">
                        <div className="flex items-center justify-between mb-4">
                            <div className="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                                <Calendar className="h-6 w-6 text-red-600 dark:text-red-400" />
                            </div>
                            <span className="text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded-full">
                                Bulan Ini
                            </span>
                        </div>
                        <h2 className="text-lg font-semibold text-zinc-800 dark:text-zinc-200 mb-1">
                            Jadwal Bulanan
                        </h2>
                        <p className="text-3xl font-bold text-red-600 dark:text-red-400">
                            {summary.currentMonthSchedulesCount}
                        </p>
                    </div>

                    <div className="bg-white dark:bg-zinc-900 rounded-xl border border-orange-100 dark:border-zinc-700 p-6 shadow-md hover:shadow-lg transition-shadow">
                        <div className="flex items-center justify-between mb-4">
                            <div className="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                <CalendarDays className="h-6 w-6 text-orange-600 dark:text-orange-400" />
                            </div>
                            <span className="text-xs font-medium text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20 px-2 py-1 rounded-full">
                                Minggu Ini
                            </span>
                        </div>
                        <h2 className="text-lg font-semibold text-zinc-800 dark:text-zinc-200 mb-1">
                            Jadwal Mingguan
                        </h2>
                        <p className="text-3xl font-bold text-orange-600 dark:text-orange-400">
                            {summary.currentWeekSchedulesCount}
                        </p>
                    </div>

                    <div className="bg-white dark:bg-zinc-900 rounded-xl border border-yellow-100 dark:border-zinc-700 p-6 shadow-md hover:shadow-lg transition-shadow">
                        <div className="flex items-center justify-between mb-4">
                            <div className="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                                <Users className="h-6 w-6 text-yellow-600 dark:text-yellow-500" />
                            </div>
                            <span className="text-xs font-medium text-yellow-600 dark:text-yellow-500 bg-yellow-50 dark:bg-yellow-900/20 px-2 py-1 rounded-full">
                                Total
                            </span>
                        </div>
                        <h2 className="text-lg font-semibold text-zinc-800 dark:text-zinc-200 mb-1">
                            Klien Terdaftar
                        </h2>
                        <p className="text-3xl font-bold text-yellow-600 dark:text-yellow-500">
                            {summary.totalAssignedClients}
                        </p>
                    </div>
                </div>

                {/* Main Content Grid */}
                <div className="grid gap-6 lg:grid-cols-2">
                    {/* Session Summary */}
                    <div className="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 shadow-md">
                        <div className="flex items-center mb-6">
                            <div className="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg mr-3">
                                <Award className="h-5 w-5 text-red-600 dark:text-red-400" />
                            </div>
                            <h2 className="text-xl font-bold text-zinc-800 dark:text-zinc-200">Ringkasan Sesi</h2>
                        </div>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="flex items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <CheckCircle className="h-5 w-5 text-green-600 dark:text-green-400 mr-3" />
                                <div>
                                    <p className="text-sm text-green-700 dark:text-green-300">Selesai</p>
                                    <p className="text-lg font-semibold text-green-800 dark:text-green-200">{summary.completedSessions}</p>
                                </div>
                            </div>

                            <div className="flex items-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <XCircle className="h-5 w-5 text-red-600 dark:text-red-400 mr-3" />
                                <div>
                                    <p className="text-sm text-red-700 dark:text-red-300">Dibatalkan</p>
                                    <p className="text-lg font-semibold text-red-800 dark:text-red-200">{summary.cancelledSessions}</p>
                                </div>
                            </div>

                            <div className="flex items-center p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                <Clock className="h-5 w-5 text-orange-600 dark:text-orange-400 mr-3" />
                                <div>
                                    <p className="text-sm text-orange-700 dark:text-orange-300">Terlewat</p>
                                    <p className="text-lg font-semibold text-orange-800 dark:text-orange-200">{summary.missedSessions}</p>
                                </div>
                            </div>

                            <div className="flex items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <CalendarCheck className="h-5 w-5 text-blue-600 dark:text-blue-400 mr-3" />
                                <div>
                                    <p className="text-sm text-blue-700 dark:text-blue-300">Total Jadwal</p>
                                    <p className="text-lg font-semibold text-blue-800 dark:text-blue-200">{summary.totalSchedules}</p>
                                </div>
                            </div>

                            <div className="flex items-center p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                <RotateCcw className="h-5 w-5 text-yellow-600 dark:text-yellow-500 mr-3" />
                                <div>
                                    <p className="text-sm text-yellow-700 dark:text-yellow-400">Sedang Berjalan</p>
                                    <p className="text-lg font-semibold text-yellow-800 dark:text-yellow-300">{summary.inProgressAssignments}</p>
                                </div>
                            </div>

                            <div className="flex items-center p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                                <Trophy className="h-5 w-5 text-emerald-600 dark:text-emerald-400 mr-3" />
                                <div>
                                    <p className="text-sm text-emerald-700 dark:text-emerald-300">Tugas Selesai</p>
                                    <p className="text-lg font-semibold text-emerald-800 dark:text-emerald-200">{summary.completedAssignments}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Package Information */}
                    <div className="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 shadow-md">
                        <div className="flex items-center mb-6">
                            <div className="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg mr-3">
                                <Package className="h-5 w-5 text-orange-600 dark:text-orange-400" />
                            </div>
                            <h2 className="text-xl font-bold text-zinc-800 dark:text-zinc-200">Analitik Paket</h2>
                        </div>

                        {/* Most Taken Package */}
                        <div className="mb-6">
                            <h3 className="text-lg font-semibold text-zinc-700 dark:text-zinc-300 mb-3 flex items-center">
                                <Trophy className="h-4 w-4 text-yellow-500 mr-2" />
                                Paket Terpopuler
                            </h3>
                            {packageSummary.mostTakenPackage ? (
                                <div className="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-zinc-800 dark:to-zinc-700 border border-yellow-200 dark:border-zinc-600 p-4 rounded-lg">
                                    <h4 className="text-lg font-semibold text-yellow-800 dark:text-yellow-300 mb-2">
                                        {packageSummary.mostTakenPackage.package.name}
                                    </h4>
                                    <div className="grid grid-cols-2 gap-2 text-sm">
                                        <p className="text-yellow-700 dark:text-yellow-400">
                                            <span className="font-medium">Kode:</span> {packageSummary.mostTakenPackage.package.code}
                                        </p>
                                        <p className="text-yellow-700 dark:text-yellow-400">
                                            <span className="font-medium">Durasi:</span> {packageSummary.mostTakenPackage.package.day_duration} hari
                                        </p>
                                        <p className="text-yellow-700 dark:text-yellow-400 col-span-2">
                                            <span className="font-medium">Diambil:</span>
                                            <span className="ml-1 bg-yellow-200 dark:bg-yellow-800 px-2 py-1 rounded-full text-xs font-bold">
                                                {packageSummary.mostTakenPackage.count}x
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            ) : (
                                <div className="bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 p-4 rounded-lg text-center">
                                    <p className="text-zinc-500 dark:text-zinc-400">Belum ada data paket tersedia.</p>
                                </div>
                            )}
                        </div>

                        {/* All Package Usage */}
                        <div>
                            <h3 className="text-lg font-semibold text-zinc-700 dark:text-zinc-300 mb-3">Penggunaan Semua Paket</h3>
                            <div className="space-y-2 max-h-48 overflow-y-auto">
                                {packageSummary.allPackageCounts.map((pkg) => (
                                    <div key={pkg.package.id} className="flex justify-between items-center p-2 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                                        <span className="text-sm text-zinc-700 dark:text-zinc-300">{pkg.package.name}</span>
                                        <span className="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-2 py-1 rounded-full text-xs font-medium">
                                            {pkg.count}x
                                        </span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>

                {/* Active Trainees */}
                <div className="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 shadow-md">
                    <div className="flex items-center mb-6">
                        <div className="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg mr-3">
                            <User className="h-5 w-5 text-red-600 dark:text-red-400" />
                        </div>
                        <h2 className="text-xl font-bold text-zinc-800 dark:text-zinc-200">Trainee Aktif</h2>
                        <span className="ml-auto bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-3 py-1 rounded-full text-sm font-medium">
                            {memberTrainee.length} Aktif
                        </span>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {memberTrainee.map((trainee) => {
                            console.log(trainee);
                            return (
                                <div
                                    key={trainee.id}
                                    className="bg-gradient-to-br from-red-50 to-orange-50 dark:from-zinc-800 dark:to-zinc-700 border border-red-100 dark:border-zinc-600 rounded-xl p-4 hover:shadow-md transition-shadow"
                                >
                                    <div className="flex items-start justify-between mb-3">
                                        <h3 className="font-semibold text-lg text-red-900 dark:text-zinc-200">
                                            {trainee.user?.name ?? 'Tidak Diketahui'}
                                        </h3>
                                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                                            trainee.status === 'active'
                                                ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                                                : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300'
                                        }`}>
                                            {trainee.status === 'active' ? 'Aktif' : trainee.status}
                                        </span>
                                    </div>

                                    <div className="space-y-2 text-sm">
                                        <div className="flex items-center text-red-700 dark:text-red-300">
                                            <Calendar className="h-4 w-4 mr-2" />
                                            <span>Mulai: {trainee.start_date}</span>
                                        </div>
                                        <div className="flex items-center text-red-700 dark:text-red-300">
                                            <CalendarCheck className="h-4 w-4 mr-2" />
                                            <span>Selesai: {trainee.end_date || 'Berlangsung'}</span>
                                        </div>
                                        <div className="flex items-center text-orange-700 dark:text-orange-300">
                                            <Timer className="h-4 w-4 mr-2" />
                                            <span>Sisa Hari: {trainee.day_left}</span>
                                        </div>
                                        <div className="flex items-center text-yellow-700 dark:text-yellow-400">
                                            <Package className="h-4 w-4 mr-2" />
                                            <span className="truncate">
                                                {trainee.personalTrainerPackage?.name ?? 'Tidak Diketahui'}
                                                ({trainee.personalTrainerPackage?.day_duration ?? '??'} hari)
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>

                    {memberTrainee.length === 0 && (
                        <div className="text-center py-8">
                            <User className="h-12 w-12 text-zinc-300 dark:text-zinc-600 mx-auto mb-3" />
                            <p className="text-zinc-500 dark:text-zinc-400">Belum ada trainee aktif saat ini</p>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
