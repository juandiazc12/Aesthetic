import { Link, useForm, usePage } from "@inertiajs/react";
import { router } from '@inertiajs/react';

import { FormEvent, useState } from "react";
import { FaEye, FaEyeSlash } from 'react-icons/fa';

export default function Login() {
    const props = usePage().props;

    const { data, post, errors, setData } = useForm({
        email: '',
        password: '',
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post('/customer/login');
    }

    const [showPassword, setShowPassword] = useState(false);
 

    return (
        <div className="flex justify-center items-center">
            <div className="mt-7 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-900 dark:border-neutral-700 w-[400px]">
                <div className="p-4 sm:p-7">
                    <div className="text-center">
                        <h1 className="block text-2xl font-bold text-gray-800 dark:text-white">Inicia sesión</h1>
                    </div>
                    <div className="mt-5">
                        <form onSubmit={handleSubmit}>
                            <div className="grid gap-y-4">
                                <div>
                                    <div className="relative">
                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            value={data.email}
                                            onChange={e => setData({ ...data, email: e.target.value })}
                                            placeholder="Correo electrónico"
                                            className="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                            required
                                            aria-describedby="email-error"
                                        />
                                    </div>
                                    {props.errors.email && (
                                        <p className="text-xs text-red-600 mt-2" id="email-error">
                                            {props.errors.email}
                                        </p>
                                    )}
                                </div>

                                <div>
                                    <div className="relative">
                                        <input
                                            type={showPassword ? "text" : "password"} // Si 'showPassword' es true, muestra el texto
                                            id="password"
                                            name="password"
                                            value={data.password}
                                            onChange={e => setData({ ...data, password: e.target.value })}
                                            placeholder="Contraseña"
                                            className="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                            required
                                            aria-describedby="password-error"
                                        />
                                        <button
                                            type="button"
                                            className="absolute inset-y-0 right-3 flex items-center justify-center"
                                            onClick={() => setShowPassword(!showPassword)}
                                        >
                                            {showPassword ? (
                                                <FaEyeSlash className="text-gray-600 dark:text-neutral-400" size={20} />
                                            ) : (
                                                <FaEye className="text-gray-600 dark:text-neutral-400" size={20} />
                                            )}
                                        </button>
                                    </div>
                                    {props.errors.password && (
                                        <p className="text-xs text-red-600 mt-2" id="password-error">
                                            {props.errors.password}
                                        </p>
                                    )}
                                </div>

                                <div className="flex items-center">
                                    <div className="flex">
                                        <input
                                            id="remember-me"
                                            name="remember-me"
                                            type="checkbox"
                                            className="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                                        />
                                    </div>
                                    <div className="ms-3">
                                        <label htmlFor="remember-me" className="text-sm dark:text-white">Acuérdate de mí</label>
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    className="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                >
                                    Iniciar sesión
                                </button>
                            </div>
                            <div className="text-center mt-4">
                                <Link
                                    className="inline-flex items-center gap-x-1 text-sm text-blue-600 decoration-2 hover:underline focus:outline-none focus:underline font-medium dark:text-blue-500"
                                    href="/customer/forgot-password"
                                >
                                    ¿Has olvidado tu contraseña?
                                </Link>

                                <p className="mt-2 text-sm text-gray-600 dark:text-neutral-400">
                                    ¿Aún no tienes una cuenta?
                                    <Link
                                        className="text-blue-600 decoration-2 hover:underline focus:outline-none focus:underline font-medium dark:text-blue-500"
                                        href="/customer/register"
                                    >
                                        Regístrate aquí
                                    </Link>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
}
