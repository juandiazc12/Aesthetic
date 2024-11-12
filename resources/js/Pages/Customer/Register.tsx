import { Link, useForm, usePage } from "@inertiajs/react";
import { FormEvent, useState } from "react";
import { FaEye, FaEyeSlash } from 'react-icons/fa';

export default function Register() {
  const { data, post, errors, setData } = useForm({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
  });

  // Estado para mostrar/ocultar contraseñas
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);



  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    post('/customer/register');
  };

  return (
    <div className="flex justify-center items-center">
      <div className="mt-7 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-900 dark:border-neutral-700 w-[400px]">
        <div className="p-4 sm:p-7">
          <div className="text-center">
            <h1 className="block text-2xl font-bold text-gray-800 dark:text-white">Registrarse</h1>
          </div>

          <div className="mt-5">
            <form onSubmit={handleSubmit}>
              <div className="grid gap-y-4">
                <div>
                  <div className="relative">
                    <input
                      type="text"
                      id="first_name"
                      name="first_name"
                      value={data.first_name}
                      onChange={e => setData({ ...data, first_name: e.target.value })}
                      placeholder="Nombres"
                      className="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                      required
                    />
                    <div className="hidden absolute inset-y-0 end-0 pointer-events-none pe-3">
                      <svg className="size-5 text-red-500" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                      </svg>
                    </div>
                  </div>
                  <p className="hidden text-xs text-red-600 mt-2" id="email-error">Ingrese su nombre válido porfavor</p>
                </div>

                <div>
                  <div className="relative">
                    <input
                      type="text"
                      id="last_name"
                      name="last_name"
                      value={data.last_name}
                      onChange={e => setData({ ...data, last_name: e.target.value })}
                      placeholder="Apellidos"
                      className="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                      required
                    />
                    <div className="hidden absolute inset-y-0 end-0 pointer-events-none pe-3">
                      <svg className="size-5 text-red-500" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                      </svg>
                    </div>
                  </div>
                  <p className="hidden text-xs text-red-600 mt-2" id="email-error">Incluya su apellidos valido porfavor</p>
                </div>

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
                    />
                    <div className="hidden absolute inset-y-0 end-0 pointer-events-none pe-3">
                      <svg className="size-5 text-red-500" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                      </svg>
                    </div>
                  </div>
                  {errors && errors.email && (
                    <p className="text-xs text-red-600 mt-2" id="email-error">{errors.email}</p>
                  )}
                </div>

                <div>
                <div className="relative">
          <input
            type={showPassword ? "text" : "password"}
            id="password"
            name="password"
            value={data.password}
            onChange={(e) => setData({ ...data, password: e.target.value })}
            placeholder="Contraseña"
            className="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm"
            required
          />
          <button
            type="button"
            className="absolute inset-y-0 right-3 flex items-center justify-center"
            onClick={() => setShowPassword(!showPassword)}
          >
            {showPassword ? (
              <FaEyeSlash className="text-gray-600" size={20} />
            ) : (
              <FaEye className="text-gray-600" size={20} />
            )}
          </button>
        </div>
                  {errors && errors.password && (
                    <p className="text-xs text-red-600 mt-2" id="password-error">{errors.password}</p>
                  )}
                </div>

                <div>
                <div className="relative">
          <input
            type={showConfirmPassword ? "text" : "password"}
            id="password_confirmation"
            name="password_confirmation"
            value={data.password_confirmation}
            onChange={(e) => setData({ ...data, password_confirmation: e.target.value })}
            placeholder="Confirmar Contraseña"
            className="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm"
            required
          />
          <button
            type="button"
            className="absolute inset-y-0 right-3 flex items-center justify-center"
            onClick={() => setShowConfirmPassword(!showConfirmPassword)}
          >
            {showConfirmPassword ? (
              <FaEyeSlash className="text-gray-600" size={20} />
            ) : (
              <FaEye className="text-gray-600" size={20} />
            )}
          </button>
        </div>
                  {errors && errors.password_confirmation && (
                    <p className="text-xs text-red-600 mt-2" id="password-confirmation-error">{errors.password_confirmation}</p>
                  )}
                </div>

                <button
                  type="submit"
                  className="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                >
                  Registrarse
                </button>
              </div>
            </form>
            <div className="text-center mt-4">
                                <p className="mt-2 text-sm text-gray-600 dark:text-neutral-400">
                                    ¿Ya tienes una cuenta? 
                                    <Link
                                        className="text-blue-600 decoration-2 hover:underline focus:outline-none focus:underline font-medium dark:text-blue-500"
                                        href="/customer/login"
                                    >
                                         Inicia sesión
                                    </Link>
                                </p>
                            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
