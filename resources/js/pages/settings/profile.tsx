"use client"

import { type BreadcrumbItem, type SharedData } from "@/types"
import { Transition } from "@headlessui/react"
import { Head, Link, useForm, usePage } from "@inertiajs/react"
import { useState, useEffect } from "react"

import DeleteUser from "@/components/delete-user"
import HeadingSmall from "@/components/heading-small"
import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import AppLayout from "@/layouts/app-layout"
import { Pencil, User, Mail, Phone, FileText, Camera, CheckCircle, Upload } from 'lucide-react'
import SettingsLayout from "@/layouts/settings/layout"

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Profile settings",
        href: "/settings/profile",
    },
]

export default function Profile({ mustVerifyEmail, status }: { mustVerifyEmail: boolean; status?: string }) {
    const { auth } = usePage<SharedData>().props
    const [imagePreview, setImagePreview] = useState<string | null>(null)

    const { data, setData, post, errors, processing, recentlySuccessful } = useForm<{
        name: string
        email: string
        phone: string
        profile_bio: string
        profile_image: File | null
    }>({
        name: auth.user.name,
        email: auth.user.email,
        phone: auth.user.phone ?? "",
        profile_bio: auth.user.profile_bio ?? "",
        profile_image: null,
    })

    function submit(event: React.FormEvent) {
        event.preventDefault()

        post(route("profile.update"), {
            preserveScroll: true,
            forceFormData: true, // IMPORTANT for file upload
        })
    }

    const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0]
        if (file) {
            setData("profile_image", file)

            // Create preview URL
            const reader = new FileReader()
            reader.onload = (e) => {
                setImagePreview(e.target?.result as string)
            }
            reader.readAsDataURL(file)
        }
    }

    // Cleanup preview URL on unmount
    useEffect(() => {
        return () => {
            if (imagePreview) {
                URL.revokeObjectURL(imagePreview)
            }
        }
    }, [imagePreview])

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Profile settings" />

            <SettingsLayout>
                <div className="min-h-screen rounded-xl mx-1 bg-gradient-to-br from-orange-50 via-red-50 to-yellow-50 dark:from-neutral-900 dark:via-neutral-800 dark:to-neutral-900 -m-6 p-6">
                    <div className="max-w-7xl mx-auto">
                        {/* Header Section */}
                        <div className="mb-8 text-center">
                            <h1 className="text-3xl font-bold bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent">
                                Profile Settings
                            </h1>
                            <p className="text-gray-600 dark:text-gray-400 mt-2">
                                Manage your account information and preferences
                            </p>
                        </div>

                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            {/* Left - Profile Card */}
                            <div className="lg:col-span-1">
                                <div className="bg-white dark:bg-neutral-800 shadow-xl rounded-2xl overflow-hidden border border-orange-200 dark:border-neutral-700">
                                    {/* Profile Header with Gradient */}
                                    <div className="bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 h-24 relative">
                                        <div className="absolute inset-0 bg-black/10"></div>
                                    </div>

                                    <div className="px-6 pb-6">
                                        {/* Profile Image */}
                                        <div className="relative -mt-12 mb-4 flex justify-center">
                                            <div className="relative group">
                                                <input
                                                    type="file"
                                                    accept="image/*"
                                                    id="profile-image-upload"
                                                    className="hidden"
                                                    onChange={handleImageChange}
                                                />
                                                <label htmlFor="profile-image-upload" className="cursor-pointer block">
                                                    <div className="w-24 h-24 rounded-full border-4 border-white dark:border-neutral-800 shadow-xl overflow-hidden bg-gray-100 dark:bg-neutral-700">
                                                        <img
                                                            src={
                                                                imagePreview ||
                                                                (auth.user.profile_image
                                                                    ? `/storage/${auth.user.profile_image}`
                                                                    : "https://placeholder.svg?height=96&width=96")
                                                            }
                                                            className="w-full h-full object-cover"
                                                            alt="Profile"
                                                            onError={(e) => {
                                                                e.currentTarget.src = "https://placeholder.svg?height=96&width=96"
                                                            }}
                                                        />
                                                    </div>
                                                    <div className="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                        <Camera className="text-white w-5 h-5" />
                                                    </div>
                                                </label>
                                                {data.profile_image && (
                                                    <div className="absolute -top-1 -right-1 bg-green-500 text-white rounded-full p-1">
                                                        <Upload className="w-3 h-3" />
                                                    </div>
                                                )}
                                            </div>
                                        </div>

                                        {/* User Info */}
                                        <div className="text-center mb-6">
                                            <h3 className="text-xl font-bold text-gray-900 dark:text-white">{auth.user.name}</h3>
                                            <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">{auth.user.email}</p>
                                            {auth.user.phone && (
                                                <p className="text-sm text-gray-600 dark:text-gray-400">{auth.user.phone}</p>
                                            )}
                                        </div>

                                        {/* Bio Section */}
                                        <div className="space-y-3">
                                            <Label htmlFor="bio" className="flex items-center gap-2 font-medium text-gray-700 dark:text-gray-300">
                                                <FileText className="w-4 h-4 text-orange-500" />
                                                Bio
                                            </Label>
                                            <textarea
                                                id="bio"
                                                value={data.profile_bio}
                                                onChange={(e) => setData("profile_bio", e.target.value)}
                                                placeholder="Ceritakan tentang diri Anda..."
                                                className="w-full p-3 text-sm text-gray-800 dark:text-white bg-gray-50 dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none transition duration-200"
                                                rows={4}
                                            />
                                            <InputError className="mt-1" message={errors.profile_bio} />
                                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                                Tuliskan deskripsi singkat tentang diri Anda, hobi, atau tujuan Anda.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Right - Form */}
                            <div className="lg:col-span-2">
                                <div className="bg-white dark:bg-neutral-800 shadow-xl rounded-2xl p-8 border border-orange-200 dark:border-neutral-700">
                                    <div className="mb-8">
                                        <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-2">Profile Information</h2>
                                        <p className="text-gray-600 dark:text-gray-400">
                                            Update your personal information and contact details
                                        </p>
                                    </div>

                                    <form onSubmit={submit} className="space-y-6">
                                        {/* Name Field */}
                                        <div className="space-y-2">
                                            <Label htmlFor="name" className="flex items-center gap-2 font-medium text-gray-700 dark:text-gray-300">
                                                <User className="w-4 h-4 text-red-500" />
                                                Full Name
                                            </Label>
                                            <div className="relative">
                                                <Input
                                                    id="name"
                                                    className="pl-10 border-gray-300 dark:border-neutral-600 focus:border-red-500 focus:ring-red-500 bg-gray-50 dark:bg-neutral-700"
                                                    value={data.name}
                                                    onChange={(e) => setData("name", e.target.value)}
                                                    required
                                                    autoComplete="name"
                                                    placeholder="Enter your full name"
                                                />
                                                <User className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                                            </div>
                                            <InputError className="mt-1" message={errors.name} />
                                        </div>

                                        {/* Email Field */}
                                        <div className="space-y-2">
                                            <Label htmlFor="email" className="flex items-center gap-2 font-medium text-gray-700 dark:text-gray-300">
                                                <Mail className="w-4 h-4 text-orange-500" />
                                                Email Address
                                            </Label>
                                            <div className="relative">
                                                <Input
                                                    id="email"
                                                    type="email"
                                                    className="pl-10 border-gray-300 dark:border-neutral-600 focus:border-orange-500 focus:ring-orange-500 bg-gray-50 dark:bg-neutral-700"
                                                    value={data.email}
                                                    onChange={(e) => setData("email", e.target.value)}
                                                    required
                                                    autoComplete="username"
                                                    placeholder="Enter your email address"
                                                />
                                                <Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                                            </div>
                                            <InputError className="mt-1" message={errors.email} />
                                        </div>

                                        {/* Phone Field */}
                                        <div className="space-y-2">
                                            <Label htmlFor="phone" className="flex items-center gap-2 font-medium text-gray-700 dark:text-gray-300">
                                                <Phone className="w-4 h-4 text-yellow-500" />
                                                Phone Number
                                            </Label>
                                            <div className="relative">
                                                <Input
                                                    id="phone"
                                                    type="tel"
                                                    className="pl-10 border-gray-300 dark:border-neutral-600 focus:border-yellow-500 focus:ring-yellow-500 bg-gray-50 dark:bg-neutral-700"
                                                    value={data.phone}
                                                    onChange={(e) => setData("phone", e.target.value)}
                                                    autoComplete="tel"
                                                    placeholder="Enter your phone number"
                                                />
                                                <Phone className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                                            </div>
                                            <InputError className="mt-1" message={errors.phone} />
                                        </div>

                                        {/* Email Verification Notice */}
                                        {mustVerifyEmail && auth.user.email_verified_at === null && (
                                            <div className="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                                <div className="flex items-start gap-3">
                                                    <div className="bg-yellow-100 dark:bg-yellow-900/30 p-1 rounded-full">
                                                        <Mail className="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                                                    </div>
                                                    <div className="flex-1">
                                                        <p className="text-sm text-yellow-800 dark:text-yellow-200">
                                                            Your email address is unverified.{" "}
                                                            <Link
                                                                href={route("verification.send")}
                                                                method="post"
                                                                as="button"
                                                                className="font-medium underline hover:no-underline"
                                                            >
                                                                Click here to resend the verification email.
                                                            </Link>
                                                        </p>

                                                        {status === "verification-link-sent" && (
                                                            <div className="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                                                                A new verification link has been sent to your email address.
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        )}

                                        {/* Submit Button */}
                                        <div className="flex items-center gap-4 pt-4">
                                            <Button
                                                disabled={processing}
                                                className="bg-gradient-to-r from-red-600 to-orange-500 hover:from-red-700 hover:to-orange-600 text-white px-8 py-2 rounded-lg font-medium shadow-lg hover:shadow-red-500/30 transition-all duration-300"
                                            >
                                                {processing ? (
                                                    <div className="flex items-center gap-2">
                                                        <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                                        Saving...
                                                    </div>
                                                ) : (
                                                    <div className="flex items-center gap-2">
                                                        <CheckCircle className="w-4 h-4" />
                                                        Save Changes
                                                    </div>
                                                )}
                                            </Button>

                                            <Transition
                                                show={recentlySuccessful}
                                                enter="transition ease-in-out duration-300"
                                                enterFrom="opacity-0 translate-y-1"
                                                enterTo="opacity-100 translate-y-0"
                                                leave="transition ease-in-out duration-300"
                                                leaveTo="opacity-0 translate-y-1"
                                            >
                                                <div className="flex items-center gap-2 text-green-600 dark:text-green-400">
                                                    <CheckCircle className="w-4 h-4" />
                                                    <span className="text-sm font-medium">Profile updated successfully!</span>
                                                </div>
                                            </Transition>
                                        </div>
                                    </form>
                                </div>

                                {/* Delete User Section */}
                                <div className="mt-8">
                                    <DeleteUser />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    )
}
