import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';

type RegisterForm = {
    name: string;
    email: string;
    phone: string;
    password: string;
    password_confirmation: string;
};

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm<Required<RegisterForm>>({
        name: '',
        email: '',
        phone: '',
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <>
            <Head title="Register" />
            <div className="h-screen md:flex">
                {/* Left Panel */}
                <div
                    className="relative overflow-hidden hidden md:flex w-1/2 bg-cover bg-center justify-around items-center"
                    style={{ backgroundImage: "url('/images/bg.jpg')" }}
                >
                    {/* Decorative Circles */}
                    <div className="absolute -bottom-32 -left-40 w-80 h-80 border-4 rounded-full border-opacity-30 border-white border-t-8"></div>
                    <div className="absolute -bottom-40 -left-20 w-80 h-80 border-4 rounded-full border-opacity-30 border-white border-t-8"></div>
                    <div className="absolute -top-40 -right-0 w-80 h-80 border-4 rounded-full border-opacity-30 border-white border-t-8"></div>
                    <div className="absolute -top-20 -right-20 w-80 h-80 border-4 rounded-full border-opacity-30 border-white border-t-8"></div>
                </div>

                {/* Right Panel */}
                <div className="flex md:w-1/2 justify-center py-10 items-center bg-white">
                    <form onSubmit={submit} className="w-full max-w-md px-6">
                        <h1 className="text-gray-800 font-bold text-2xl mb-1">Platinum GYM</h1>
                        <p className="text-sm font-normal text-gray-600 mb-6">Enter your details below to create your account</p>

                        <div className="space-y-4">
                            {/* Name */}
                            <div className="flex items-center border-2 py-2 px-3 rounded-2xl text-black">
                                <input
                                    id="name"
                                    type="text"
                                    placeholder="Full name"
                                    className="w-full pl-2 outline-none border-none placeholder-gray-600"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    disabled={processing}
                                    required
                                />
                            </div>
                            <InputError message={errors.name} />

                            {/* Email */}
                            <div className="flex items-center border-2 py-2 px-3 rounded-2xl text-black">
                                <input
                                    id="email"
                                    type="email"
                                    placeholder="Email address"
                                    className="w-full pl-2 outline-none border-none placeholder-gray-600"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    disabled={processing}
                                    required
                                />
                            </div>
                            <InputError message={errors.email} />

                            {/* Phone */}
                            <div className="flex items-center border-2 py-2 px-3 rounded-2xl text-black">
                                <input
                                    id="phone"
                                    type="tel"
                                    placeholder="Phone number"
                                    className="w-full pl-2 outline-none border-none placeholder-gray-600"
                                    value={data.phone}
                                    onChange={(e) => setData('phone', e.target.value)}
                                    disabled={processing}
                                    required
                                />
                            </div>
                            <InputError message={errors.phone} />

                            {/* Password */}
                            <div className="flex items-center border-2 py-2 px-3 rounded-2xl text-black">
                                <input
                                    id="password"
                                    type="password"
                                    placeholder="Password"
                                    className="w-full pl-2 outline-none border-none placeholder-gray-600"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    disabled={processing}
                                    required
                                />
                            </div>
                            <InputError message={errors.password} />

                            {/* Confirm Password */}
                            <div className="flex items-center border-2 py-2 px-3 rounded-2xl text-black">
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    placeholder="Confirm password"
                                    className="w-full pl-2 outline-none border-none placeholder-gray-600"
                                    value={data.password_confirmation}
                                    onChange={(e) => setData('password_confirmation', e.target.value)}
                                    disabled={processing}
                                    required
                                />
                            </div>
                            <InputError message={errors.password_confirmation} />

                            {/* Submit Button */}
                            <Button type="submit" className="w-full bg-[#7AE2CF] mt-4 py-2 rounded-2xl text-white font-semibold" disabled={processing}>
                                {processing && <LoaderCircle className="h-4 w-4 animate-spin mr-2 inline" />}
                                Create Account
                            </Button>

                            {/* Link to Login */}
                            <div className="text-center text-sm mt-2 text-[#7AE2CF]">
                                Already have an account?{' '}
                                <TextLink href={route('login')} className="text-[#7AE2CF] hover:underline">
                                    Log in
                                </TextLink>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}
