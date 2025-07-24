import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function ForgotPassword({ status }: { status?: string }) {
    const { jetstream } = usePage().props as any;
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post('/customer/forgot-password');
    };

    return (
        <div className="flex justify-center items-center min-h-screen bg-gray-100 p-4">
            <div className="w-full max-w-md bg-white rounded-lg shadow-md overflow-hidden">
                <div className="p-6">
                    <h2 className="text-2xl font-bold text-center text-gray-800 mb-6">Restablecer contraseña</h2>
                    
                    <div className="mb-6 text-sm text-gray-600">
                        ¿Olvidaste tu contraseña? No hay problema. Indícanos tu dirección de correo electrónico y te enviaremos un enlace para que puedas establecer una nueva contraseña.
                    </div>

                    {status === 'passwords.sent' && (
                        <div className="mb-6 p-3 bg-green-100 text-green-700 text-sm rounded">
                            Hemos enviado un enlace de recuperación a tu correo electrónico. Por favor, revisa tu bandeja de entrada y sigue las instrucciones para restablecer tu contraseña.
                        </div>
                    )}
                    {status && status !== 'passwords.sent' && (
                        <div className="mb-6 p-3 bg-yellow-100 text-yellow-700 text-sm rounded">
                            {status}
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-6">
                        <div>
                            <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
                                Correo electrónico
                            </label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value={data.email}
                                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="tu@ejemplo.com"
                                autoComplete="email"
                                onChange={(e) => setData('email', e.target.value)}
                                required
                                autoFocus
                            />
                            {errors.email && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.email}
                                </p>
                            )}
                        </div>

                        <div className="flex items-center justify-end">
                            <button
                                type="submit"
                                className="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                                disabled={processing}
                            >
                                {processing ? 'Enviando...' : 'Enviar enlace de recuperación'}
                            </button>
                        </div>
                    </form>

                    <div className="mt-6 text-center">
                        <Link
                            href="/customer/login"
                            className="text-sm text-blue-600 hover:text-blue-800 hover:underline"
                        >
                            ← Volver al inicio de sesión
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}
