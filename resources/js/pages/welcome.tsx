import { Head, Link } from '@inertiajs/react';
import AppearanceToggle from '@/components/appearance-compact';
import { PageProps } from '@/types';

export default function Welcome({ auth }: PageProps<{ auth: any }>) {
    return (
        <>
            <Head title="Welcome to Platinum Gym" />
            <div className="relative max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 min-h-screen flex items-center">

            {/* Appearance Toggle Positioned Top-Right */}
            <div className="absolute top-4 right-4 z-50">
                <AppearanceToggle />
            </div>

            <div className="grid md:grid-cols-2 gap-4 md:gap-8 xl:gap-20 md:items-center">
                <div>
                    <h1 className="block text-3xl font-bold text-gray-800 sm:text-4xl lg:text-6xl lg:leading-tight dark:text-white">
                        Start your journey with <span className="text-[#F61501]">Platinum Gym</span>
                    </h1>
                    <p className="mt-3 text-lg text-[#D38442] dark:text-[#D38442]">
                        Power in Every Rep. Platinum in Every Step.
                    </p>

                    <div className="mt-7 grid gap-3 w-full sm:inline-flex">
                        {auth.user ? (
                            <Link
                                href={route('dashboard')}
                                className="py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700"
                            >
                                Dashboard
                                <svg className="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2"
                                          d="M9 5l7 7-7 7"/>
                                </svg>
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href={route('login')}
                                    className="py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg bg-[#D38442] text-white shadow-sm hover:bg-[#b56b34] focus:outline-none focus:ring-2 focus:ring-[#D38442] focus:ring-offset-2 dark:bg-[#D38442] dark:hover:bg-[#b56b34]"
                                >
                                    Log in
                                </Link>
                                <Link
                                    href={route('register')}
                                    className="py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#F61501] text-white hover:bg-[#c41201] focus:outline-none focus:ring-2 focus:ring-[#F61501] focus:ring-offset-2"
                                >
                                    Register
                                </Link>
                            </>
                        )}
                    </div>
                </div>

                <div className="hidden md:block">
                    <img src="/images/bg.jpg" alt="Hero Illustration" className="w-full" />
                </div>
            </div>
        </div>
        </>
        );
}
