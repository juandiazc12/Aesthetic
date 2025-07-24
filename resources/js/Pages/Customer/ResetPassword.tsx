import { Head, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function ResetPassword({ token, email, status }: { token: string; email: string; status?: string }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        token: token,
        email: email,
        password: '',
        password_confirmation: '',
    });

    const { jetstream } = usePage().props as any;

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post('/customer/reset-password', {
            onSuccess: () => {
                reset();
                // Redirigir al login con mensaje de éxito
                window.location.href = '/customer/login?reset=success';
            },
        });
    };

    return (
        <div className="flex justify-center items-center min-h-screen bg-gray-100 p-4">
            <div className="w-full max-w-md bg-white rounded-lg shadow-md overflow-hidden">
                <div className="p-6">
                    <h2 className="text-2xl font-bold text-center text-gray-800 mb-6">Restablecer contraseña</h2>
                    
                    {status === 'passwords.reset' && (
                        <div className="mb-6 p-3 bg-green-100 text-green-700 text-sm rounded">
                            Tu contraseña ha sido restablecida exitosamente. Ahora puedes iniciar sesión con tu nueva contraseña.
                        </div>
                    )}
                    
                    <div className="mb-6 text-sm text-gray-600">
                        Por favor, ingresa tu correo electrónico y establece una nueva contraseña para tu cuenta.
                    </div>

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
                                onChange={(e) => setData('email', e.target.value)}
                                required
                                autoFocus
                                disabled={processing}
                            />
                            {errors.email && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.email}
                                </p>
                            )}
                        </div>

                        <div>
                            <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-1">
                                Nueva contraseña
                            </label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                value={data.password}
                                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Mínimo 8 caracteres"
                                autoComplete="new-password"
                                onChange={(e) => setData('password', e.target.value)}
                                required
                                disabled={processing}
                            />
                            {errors.password && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.password}
                                </p>
                            )}
                        </div>

                        <div>
                            <label htmlFor="password_confirmation" className="block text-sm font-medium text-gray-700 mb-1">
                                Confirmar contraseña
                            </label>
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                value={data.password_confirmation}
                                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Vuelve a escribir tu contraseña"
                                autoComplete="new-password"
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                required
                                disabled={processing}
                            />
                            {errors.password_confirmation && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.password_confirmation}
                                </p>
                            )}
                        </div>

                        <div className="flex items-center justify-end">
                            <button
                                type="submit"
                                className="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50"
                                disabled={processing}
                            >
                                {processing ? 'Procesando...' : 'Restablecer contraseña'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}
