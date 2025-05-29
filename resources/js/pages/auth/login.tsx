import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';

type LoginForm = {
    email: string;
    password: string;
    remember: boolean;
};

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) {
    const { data, setData, post, processing, errors, reset } = useForm<Required<LoginForm>>({
        email: '',
        password: '',
        remember: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <>
            <Head title="Log in" />
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
                        <h1 className="text-gray-800 font-bold text-2xl mb-1">Welcome Back</h1>
                        <p className="text-sm font-normal text-gray-600 mb-6">Log in to your account</p>

                        <div className="space-y-4">
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

                            <div className="flex justify-between items-center text-sm">
                                <label className="flex items-center space-x-2">
                                    <input
                                        type="checkbox"
                                        id="remember"
                                        name="remember"
                                        checked={data.remember}
                                        onChange={() => setData('remember', !data.remember)}
                                        className="form-checkbox"
                                    />
                                    <span className='text-[#F61501]'>Remember me</span>
                                </label>
                                {canResetPassword && (
                                    <TextLink href={route('password.request')} className="text-sm text-[#F61501] hover:underline">
                                        Forgot password?
                                    </TextLink>
                                )}
                            </div>

                            <Button
                                type="submit"
                                className="w-full bg-[#F61501] mt-4 py-2 rounded-2xl text-white font-semibold"
                                disabled={processing}
                            >
                                {processing && <LoaderCircle className="h-4 w-4 animate-spin mr-2 inline" />}
                                Log in
                            </Button>

                            <div className="text-center text-sm mt-2 text-[#F61501]">
                                Don't have an account?{' '}
                                <TextLink href={route('register')} className="text-[#F61501] hover:underline">
                                    Sign up
                                </TextLink>
                            </div>

                            {status && (
                                <div className="text-center text-sm font-medium text-green-600 mt-2">{status}</div>
                            )}
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}
