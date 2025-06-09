import { type BreadcrumbItem, type SharedData } from '@/types';
import { Transition } from '@headlessui/react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

import DeleteUser from '@/components/delete-user';
import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout'
import { Pencil } from 'lucide-react';
import SettingsLayout from '@/layouts/settings/layout';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];



export default function Profile({ mustVerifyEmail, status }: { mustVerifyEmail: boolean; status?: string }) {
    const { auth } = usePage<SharedData>().props;

    const { data, setData, post, errors, processing, recentlySuccessful } = useForm<{
        name: string;
        email: string;
        profile_bio: string;
        profile_image: File | null;
    }>({
        name: auth.user.name,
        email: auth.user.email,
        profile_bio: auth.user.profile_bio ?? '',
        profile_image: null,
    });


    function submit(event: React.FormEvent) {
        event.preventDefault();

        post(route('profile.update'), {
            preserveScroll: true,
            forceFormData: true, // IMPORTANT for file upload
        });
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Profile settings" />

            <SettingsLayout>
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Kiri - Form Profil */}
                    <div className="lg:col-span-2 space-y-6">
                        <HeadingSmall title="Profile information" description="Update your name and email address" />

                        <form onSubmit={submit} className="space-y-6">
                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    className="mt-1 block w-full"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    required
                                    autoComplete="name"
                                    placeholder="Full name"
                                />
                                <InputError className="mt-2" message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Email address</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    className="mt-1 block w-full"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    required
                                    autoComplete="username"
                                    placeholder="Email address"
                                />
                                <InputError className="mt-2" message={errors.email} />
                            </div>

                            {mustVerifyEmail && auth.user.email_verified_at === null && (
                                <div>
                                    <p className="text-muted-foreground -mt-4 text-sm">
                                        Your email address is unverified.{' '}
                                        <Link
                                            href={route('verification.send')}
                                            method="post"
                                            as="button"
                                            className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                        >
                                            Click here to resend the verification email.
                                        </Link>
                                    </p>

                                    {status === 'verification-link-sent' && (
                                        <div className="mt-2 text-sm font-medium text-green-600">
                                            A new verification link has been sent to your email address.
                                        </div>
                                    )}
                                </div>
                            )}

                            <div className="flex items-center gap-4">
                                <Button disabled={processing}>Save</Button>
                                <Transition
                                    show={recentlySuccessful}
                                    enter="transition ease-in-out"
                                    enterFrom="opacity-0"
                                    leave="transition ease-in-out"
                                    leaveTo="opacity-0"
                                >
                                    <p className="text-sm text-neutral-600">Saved</p>
                                </Transition>
                            </div>
                        </form>

                        <DeleteUser />
                    </div>

                    {/* Kanan - Foto dan Bio */}
                    <div className="bg-white bg dark:bg-sidebar shadow-lg rounded-xl p-6">
                        <div className="flex flex-col items-center text-center">
                            <div className="relative group w-32 h-32 -mt-16 mb-4">
                                <input
                                    type="file"
                                    accept="image/*"
                                    id="profile-image-upload"
                                    className="hidden"
                                    onChange={(e) => {
                                        const file = e.target.files?.[0];
                                        if (file) {
                                            setData('profile_image', file); // set the actual File object
                                        }
                                    }}
                                />
                                <label htmlFor="profile-image-upload" className="cursor-pointer block w-full h-full">
                                    <img
                                        src={`/storage/${auth.user.profile_image}`}
                                        className="w-full h-full rounded-full shadow-xl object-cover"
                                        alt="Profile"
                                    />
                                    <div className="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <Pencil className="text-white w-6 h-6" />
                                    </div>
                                </label>
                            </div>

                            <h3 className="text-xl font-semibold text-black dark:text-white">{auth.user.name}</h3>
                            <p className="text-sm text-black dark:text-white mt-1">{auth.user.email}</p>

                            <div className="mt-4 w-full">
                                <Label htmlFor="bio" className="block text-left mb-1 font-medium text-sm text-black dark:text-white">
                                    Bio
                                </Label>
                                <textarea
                                    id="bio"
                                    value={data.profile_bio}
                                    onChange={(e) => setData('profile_bio', e.target.value)}
                                    placeholder="Ceritakan tentang diri Anda..."
                                    className="w-full p-3 text-sm text-gray-800 dark:text-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none transition duration-200"
                                    rows={4}
                                />
                                <InputError className="mt-2" message={errors.profile_bio} />
                                <p className="mt-1 text-xs text-gray-500 dark:text-white">Tuliskan deskripsi singkat tentang diri Anda, hobi, atau tujuan Anda.</p>
                            </div>

                        </div>
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
